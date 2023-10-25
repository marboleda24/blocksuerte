<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use mysql_xdevapi\Exception;

class UserController extends Controller
{
    /**
     * __contract
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:administration.users');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/Users/Index', [
            'data' => User::all(),
        ]);
    }

    /**
     * show
     *
     * @param  mixed  $user
     * @return Response
     */
    public function show(User $user): Response
    {
        $userPermissions = $user->getAllPermissions();

        return Inertia::render('Applications/Users/Show', [
            'user' => $user,
            'userPermissions' => $userPermissions,
            'roles' => $user->roles,
        ]);
    }

    /**
     * edit
     *
     * @param  mixed  $user
     * @return Response
     */
    public function edit(User $user): Response
    {
        if (Auth::user()->hasRole('super-admin')) {
            // Son los roles que el usuario aún no tiene y está disponible su asignación
            $availableRoles = Role::whereDoesntHave('users', function (Builder $query) use ($user) {
                $query->where('id', $user->id);
            })->get();
        } else {
            // Son los roles que el usuario aún no tiene y está disponible su asignación. Además no son roles del sistema.
            $availableRoles = Role::where('protected', 0)
                ->whereDoesntHave('users', function (Builder $query) use ($user) {
                    $query->where('id', $user->id);
                })->get();
        }

        // Son todos los permisos directos de la plataforma que el usuario aún no tiene
        $allAvailablePermissions = Permission::whereDoesntHave('users', function (Builder $query) use ($user) {
            $query->where('id', $user->id);
        })->get();

        // Son los permisos heredados de los roles que posee el usuario
        $inheritedPermissions = $user->getPermissionsViaRoles();

        // Se clasifican todos los permisos heredados de los roles a cada grupo perteneciente
        $inheritedPermissionsGroups = $inheritedPermissions->groupBy('group_id')->transform(function ($item, $key) {
            $permissionGroup = PermissionGroup::find($key);

            return ['id' => $permissionGroup->id, 'name' => $permissionGroup->name, 'permissions' => $item];
        })->values();

        // Son los permisos asociados a el usuario
        $associatedPermissions = $user->permissions;

        // Se clasifican todos los permisos asociados al usuario a cada grupo perteneciente
        $associatedPermissionsGroups = $associatedPermissions->groupBy('group_id')->transform(function ($item, $key) {
            $permissionGroup = PermissionGroup::find($key);

            return ['id' => $permissionGroup->id, 'name' => $permissionGroup->name, 'permissions' => $item];
        })->values();

        // Son los permisos que están disponibles para asignar a el usuario
        $availablePermissions = $allAvailablePermissions->diff($inheritedPermissions);

        // Se clasifican todos los permisos que están disponibles para asignar a los grupos pertenecientes
        $availablePermissionsGroups = $availablePermissions->groupBy('group_id')->transform(function ($item, $key) {
            $permissionGroup = PermissionGroup::find($key);

            return ['id' => $permissionGroup->id, 'name' => $permissionGroup->name, 'permissions' => $item];
        })->values();

        return Inertia::render('Applications/Users/Edit', [
            'user' => $user,
            'availableRoles' => $availableRoles,
            'inheritedPermissions' => $inheritedPermissions,
            'inheritedPermissionsGroups' => $inheritedPermissionsGroups,
            'associatedPermissions' => $associatedPermissions,
            'associatedPermissionsGroups' => $associatedPermissionsGroups,
            'availablePermissions' => $availablePermissions,
            'availablePermissionsGroups' => $availablePermissionsGroups,

        ]);
    }

    /**
     * update
     *
     * @param mixed $request
     * @param mixed $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // Obtiene información de campos del formulario y si no encuentra valor, asigna un array vacío
        $assignedRoles = $request->get('assigned_roles', []);
        $availableRoles = $request->get('avail_roles', []);
        $assignedPermissions = $request->get('assigned_perms', []);
        $availablePermissions = $request->get('avail_perms', []);

        // Se mezclan los roles y permisos ya asignados al usuario con los nuevos asociados
        $userRoles = array_merge($assignedRoles, $availableRoles);
        $userPerms = array_merge($assignedPermissions, $availablePermissions);

        // Asocia los roles y permisos a el usuario
        $user->syncRoles($userRoles);
        $user->syncPermissions($userPerms);
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        return response()->json('user permissions updated successfully', 200);
    }

    /**
     * get online users platform
     *
     * @return JsonResponse
     */
    public function online_users(): JsonResponse
    {
        $users = User::whereBetween('last_action', [now()->subMinutes(1), now()])->get();

        return response()->json($users);
    }

    public function create()
    {
        $Permission = PermissionGroup::with('permissions')->get();
        $availableRoles = Role::where('name', '!=', 'super-admin')->get();

        return Inertia::render('Applications/Users/Create', [
            'Permission' => $Permission,
            'availableRoles' => $availableRoles
        ]);

    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Request $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $assignedRoles = $request->get('assigned_roles', []);
            $availableRoles = $request->from['avail_roles'];
            $assignedPermissions = $request->get('assigned_perms', []);
            $availablePermissions = $request->from['avail_perms'];

            $userRoles = array_merge($assignedRoles, $availableRoles);
            $userPerms = array_merge($assignedPermissions, $availablePermissions);


            $user->syncRoles($userRoles);
            $user->syncPermissions($userPerms);
            $user->updated_at = date('Y-m-d H:i:s');
            $user->name = $request->from['name'];
            $user->email = $request->from['email'];
            $user->username = $request->from['users'];
            $user->password = Hash::make($request->from['password']);
            $user->save();
            DB::commit();
            return response()->json('succes', 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e, 500);
        }


    }

}
