<?php

namespace App\Http\Controllers;

use App\Models\RemoteAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RemoteAccessController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:remote-access', ['only' => ['index']]);
        $this->middleware('permission:remote_access.create', ['only' => ['store']]);
        $this->middleware('permission:remote_access.create', ['only' => ['destroy']]);
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = RemoteAccess::orderBy('name', 'asc')->get();

        return Inertia::render('Applications/RemoteAccess', ['remote_access' => $data]);
    }

    /**
     * store
     *
     * @param  mixed  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'url' => ['required'],
            'icon' => ['required'],
        ])->validate();

        RemoteAccess::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'created_by' => auth()->user()->id,
        ]);

        $data = RemoteAccess::orderBy('name', 'asc')->get();

        return response()->json($data, 200);
    }

    /**
     * destroy
     *
     * @param  mixed  $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->has('id')) {
            RemoteAccess::find($request->input('id'))->delete();

            return redirect()->back();
        }
    }
}
