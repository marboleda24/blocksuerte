<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PermissionGroupController extends Controller
{
    /**
     * PermissionController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:administration.permissions-group.view', [
            'only' => ['index', 'show', 'edit'],
        ]);

        $this->middleware('permission:administration.permissions-group.create', [
            'only' => ['create', 'store', 'update', 'destroy'],
        ]);
    }

    /**
     * Muestra una lista de todos los grupos de permisos de la plataforma.
     *
     * @return Response
     */
    public function index(): Response
    {
        $permissionGroups = PermissionGroup::all();

        return Inertia::render('Applications/PermissionGroups/Index', ['data' => $permissionGroups]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        // Únicamente un 'super-admin' puede asociar permisos del sistema a un grupo
        if (Auth::user()->hasRole('super-admin')) {
            // Obtiene todos los permisos del grupo 'Estándar' para asignar a nuevos grupos
            $permissions = Permission::where('group_id', 1)
                ->select('description', 'id')->get();
        } else {
            // Obtiene todos los permisos que no estén protegidos y sean del grupo 'Estándar'
            $permissions = Permission::where(['group_id' => 1, 'protected' => 0])
                ->select('description', 'id')->get();
        }

        return Inertia::render('Applications/PermissionGroups/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Almacena un nuevo grupo de permisos en la plataforma
     * y asocia permisos a éste.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $permissionGroup = PermissionGroup::create($request->except('permissions'));

        if ($permissionGroup->save()) {
            $permissions = $request->get('permissions');

            Permission::whereIn('id', $permissions)
                ->update(['group_id' => $permissionGroup->id]);

            return response()->json([
                'message' => 'permission group created successfully',
            ], 200);
        }

        return response()->json('error creating permission group', 500);
    }

    /**
     * Proporciona la información de un grupo de permisos en específico
     * junto con sus permisos asociados.
     *
     * @param  PermissionGroup  $permissionGroup
     * @return Response
     */
    public function show(PermissionGroup $permissionGroup): Response
    {
        return Inertia::render('Applications/PermissionGroups/Show', [
            'permissionGroup' => $permissionGroup,
            'permissions' => $permissionGroup->permissions,
        ]);
    }

    /**
     * Muestra el formulario para editar la información de un
     * grupo de permisos en específico.
     *
     * @param  PermissionGroup  $permissionGroup
     * @return Response
     */
    public function edit(PermissionGroup $permissionGroup): Response
    {
        // Únicamente un 'super-admin' puede asociar permisos del sistema a un grupo
        if (Auth::user()->hasRole('super-admin')) {
            // Obtiene todos los permisos que no estén asociados al grupo incluso los protegidos
            $availablePermissions = Permission::whereDoesntHave('group', function (Builder $query) use ($permissionGroup) {
                $query->where('id', $permissionGroup->id);
            })
            ->pluck('description', 'id');
        } else {
            // Obtiene todos los permisos que no estén asociados al grupo pero que no estén protegidos
            $availablePermissions = Permission::where('protected', 0)
                ->whereDoesntHave('group', function (Builder $query) use ($permissionGroup) {
                    $query->where('id', $permissionGroup->id);
                })
                ->pluck('description', 'id');
        }

        return Inertia::render('Applications/PermissionGroups/Edit', [
            'permissionGroup' => $permissionGroup,
            'availablePermissions' => $availablePermissions,
            'permissions' => $permissionGroup->permissions,
        ]);
    }

    /**
     * Actualiza la información de un grupo de permisos en específico
     * junto con sus nuevos permisos.
     *
     * @param  Request  $request
     * @param  PermissionGroup  $permissionGroup
     * @return RedirectResponse
     */
    public function update(Request $request, PermissionGroup $permissionGroup): RedirectResponse
    {
        // Son los valores de los campos del formulario, si no hay un valor se les asigna un array vacío
        $selectedPermissions = $request->get('assigned_perms', []);
        $availablePermissions = $request->get('avail_perms', []);

        // Son los permisos que ya están asociados al grupo
        $relatedPermissions = $permissionGroup->permissions()->pluck('id');

        // Son los permisos que estaban asociados al grupo, pero se desmarco su selección para desasociarlos
        $excludedPermissions = array_diff($relatedPermissions->toArray(), $selectedPermissions);

        // Se fusionan los permisos ya asignados anteriormente al grupo con los nuevos seleccionados por el usuario
        $permissionsToAssign = array_merge($selectedPermissions, $availablePermissions);

        // Se respalda el grupo al que pertenecía cada permiso que se va a mover a un nuevo grupo
        $previousPermissionGroups = array_map(function ($group) {
            return $group['id'];
        }, (array) Permission::getGroups($availablePermissions));

        // Actualiza la informacion estándar del grupo
        $permissionGroup->fill($request->except('assigned_perms', 'avail_perms'));

        if (! empty($excludedPermissions)) {
            // Al desmarcar permisos asociados a un grupo, estos se deben de mover al grupo de permisos 'Estándar'
            Permission::whereIn('id', $excludedPermissions)->update([
                'group_id' => '1',
            ]);

            // Actualiza la fecha de modificación del grupo de permisos 'Estándar'
            PermissionGroup::where('id', 1)->update([
                'updated_at' => Carbon::now(),
            ]);
        }

        // Se asigna la fusión de permisos a el grupo
        Permission::whereIn('id', $permissionsToAssign)->update([
            'group_id' => $permissionGroup->id,
        ]);

        $permissionGroup->touch();

        if (! empty($previousPermissionGroups)) {
            // Actualiza la fecha de modificación del grupo de permisos al que pertenecía cada permiso que se movió a un nuevo grupo
            PermissionGroup::whereIn('id', $previousPermissionGroups)->update([
                'updated_at' => Carbon::now(),
            ]);
        }

        return redirect()
            ->route('permission_groups.edit', $permissionGroup->id)
            ->with([
                'message' => 'Grupo de permisos actualizado con éxito.',
                'alert-type' => 'success',
            ]);
    }

    /**
     * Elimina un grupo de permisos específico de la plataforma
     *
     * @param  PermissionGroup  $permissionGroup
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(PermissionGroup $permissionGroup): RedirectResponse
    {
        if ($permissionGroup->delete()) {
            return redirect()->route('permission_groups.index')
                ->with([
                    'message' => 'Grupo de permisos eliminado con éxito.',
                    'alert-type' => 'success',
                ]);
        }

        return redirect()->route('permission_groups.index')
            ->with([
                'message' => 'Grupo de permisos no eliminado.',
                'alert-type' => 'error',
            ]);
    }
}
