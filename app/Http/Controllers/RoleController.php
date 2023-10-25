<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Protects controller methods using permissions
     * and using the role and permission package middleware.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:administration.roles.view', [
            'only' => ['index', 'edit', 'show', 'roles_list'],
        ]);

        $this->middleware('permission:administration.roles.create', [
            'only' => ['destroy', 'create', 'store', 'update'],
        ]);
    }

    /**
     * Displays a list of all platform roles
     *
     * @return Response
     */
    public function index(): Response
    {
        $roles = Role::all();

        return Inertia::render('Applications/Roles/Index', ['data' => $roles]);
    }

    /**
     * Shows the form to create a new role on the platform.
     *
     * @return Response
     */
    public function create(): Response
    {
        if (Auth::user()->hasRole('super-admin')) {
            // Obtiene todos los permisos de la plataforma con todos sus permisos asociados
            $permissionGroups = PermissionGroup::with('permissions')->get();
        } else {
            // Obtiene todos los grupos de permisos de la plataforma, pero discrimina los permisos asociados al grupo que sean del sistema
            $permissionGroups = PermissionGroup::with(['permissions' => function ($query) {
                $query->where('protected', 0);
            }])->get();
        }

        $permissionsQuantity = array_sum(array_map(function ($permissionGroup) {
            return count($permissionGroup['permissions']);
        }, $permissionGroups->toArray()));

        return Inertia::render('Applications/Roles/Create', [
            'permissionGroups' => $permissionGroups,
            'permissionsQuantity' => $permissionsQuantity,
        ]);
    }

    /**
     * Store a new role on the platform along with its associated permissions.
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::create($request->except('permissions'));
            $role->syncPermissions($request->get('permissions', []));
            $roles = Role::all();

            DB::commit();

            return response()->json($roles, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Provides information for a specific role along with associated users and permissions.
     *
     * @param  mixed  $role
     * @return Response
     */
    public function show(Role $role): Response
    {
        // Obtiene los permisos asociados al rol
        $rolePermissions = $role->permissions()->get();

        // Obtiene los usuarios que poseen el rol
        $roleUsers = User::role($role->name)->get();

        return Inertia::render('Applications/Roles/Show', [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
            'roleUsers' => $roleUsers,
        ]);
    }

    /**
     * Displays the form to edit the information for a specific role.
     *
     * @param  mixed  $role
     * @return JsonResponse|Response
     */
    public function edit(Role $role): Response
    {
        // Únicamente un 'super-admin' puede editar el rol de 'super-admin' o 'admin'
        if ($role->name == 'super-admin' || $role->name == 'admin') {
            if (! Auth::user()->hasRole('super-admin')) {
                return response()->json('No tienes permiso para editar este rol', 500);
            }
        }

        // Obtiene todos los permisos de la plataforma con todos sus permisos asociados
        $permissionGroups = PermissionGroup::with('permissions')->get();

        // Obtiene los permisos asociados al rol
        $rolePermissions = $role->permissions()->get();

        $permissionsQuantity = array_sum(array_map(function ($permissionGroup) {
            return count($permissionGroup['permissions']);
        }, $permissionGroups->toArray()));

        return Inertia::render('Applications/Roles/Edit', [
            'role' => $role,
            'permissionGroups' => $permissionGroups,
            'rolePermissions' => $rolePermissions,
            'permissionsQuantity' => $permissionsQuantity,
        ]);
    }

    /**
     * Update the information for a specific role along with their new permissions.
     *
     * @param  mixed  $request
     * @param  mixed  $role
     * @return JsonResponse
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        if ($request->input('protected') === null) {
            $role->protected = '0';
        }

        $role->fill($request->except('permissions'));
        $role->syncPermissions($request->get('permissions', []));
        $role->updated_at = date('Y-m-d H:i:s');
        $role->save();

        return response()->json('Rol actualizado con exito!', 200);
    }

    /**
     * Delete a specific role from the platform.
     *
     * @param  mixed  $role
     * @return RedirectResponse|JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->delete()) {
            $roles = Role::all();

            return response()->json($roles, 200);
        } else {
            return response()->json('No se elimino el rol', 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function roles_list(): JsonResponse
    {
        $data = auth()->user()->getAllPermissions()->pluck('name');

        return response()->json($data, 200);
    }
}
