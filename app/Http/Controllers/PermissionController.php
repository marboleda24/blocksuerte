<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    /**
     * PermissionController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:administration.permissions.view', [
            'only' => ['index', 'show', 'edit', 'permissions_list'],
        ]);

        $this->middleware('permission:administration.permissions.create', [
            'only' => ['create', 'store', 'update', 'destroy'],
        ]);
    }

    /**
     * Muestra una lista de todos los permisos de la plataforma.
     *
     * @return Response
     */
    public function index(): Response
    {
        $permissions = Permission::all();

        return Inertia::render('Applications/Permissions/Index', [
            'data' => $permissions,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo permiso en la plataforma.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Applications/Permissions/Create');
    }

    /**
     * @param  Request  $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|min:1|unique:permissions,name',
            'description' => 'required|min:4',
        ])->validate();

        Permission::create($request->all());

        return response('Permission created successfully', 200);
    }

    /**
     * Proporciona la información de un permiso en específico
     * junto con los usuarios y roles asociados.
     *
     * @param  Permission  $permission
     * @return Response
     */
    public function show(Permission $permission): Response
    {
        // Obtiene los usuarios con este permiso asociado
        $permissionUsers = User::permission($permission->name)->get();

        return Inertia::render('Applications/Permissions/Show', [
            'permission' => $permission,
            'permission_roles' => $permission->roles,
            'permissionUsers' => $permissionUsers,
        ]);
    }

    /**
     * Muestra el formulario para editar la información de un
     * permiso en específico.
     *
     * @param  Permission  $permission
     * @return Response
     */
    public function edit(Permission $permission): Response
    {
        return Inertia::render('Applications/Permissions/Edit', [
            'data' => $permission,
        ]);
    }

    /**
     * Actualiza la información de un permiso en específico.
     *
     * @param  Request  $request
     * @param  Permission  $permission
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        Validator::make($request->all(), [
            'name' => 'required|min:1|unique:permissions,name,'.$request->name.',name',
            'description' => 'required|min:4',
        ])->validate();

        if ($request->get('protected') === null) {
            $permission->protected = '0';
        }

        $permission->fill($request->all());
        $permission->save();

        return response()->json('permission updated successfully', 200);
    }

    /**
     * Elimina un permiso en específico de la plataforma.
     *
     * @param  Permission  $permission
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->delete()) {
            return redirect()->route('permissions.index')
                ->with([
                    'message' => 'Permiso eliminado con éxito.',
                    'alert-type' => 'success',
                ]);
        } else {
            return redirect()->route('permissions.index')
                ->with([
                    'message' => 'Permiso no eliminado.',
                    'alert-type' => 'error',
                ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function permissions_list(): JsonResponse
    {
        $data = auth()->user()->getAllPermissions()->pluck('name');
        return response()->json($data, 200);
    }
}
