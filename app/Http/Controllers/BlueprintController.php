<?php

namespace App\Http\Controllers;

use App\Models\Blueprint;
use App\Models\EncoderLine;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlueprintController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $blueprints = Blueprint::with('files', 'created_user')->get();

        $lines = EncoderLine::whereHas('product_types', function ($q) {
            $q->whereNotIn('product_type_code', ['S']);
        })->orderBy('name', 'asc')->get();

        return Inertia::render('Applications/Blueprint/Index', [
            'blueprints' => $blueprints,
            'lines' => $lines,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $miniature = base64_encode(file_get_contents($request->file('miniature')->path()));
            $miniature = "data:image/{$request->file('miniature')->getClientOriginalExtension()};base64,{$miniature}";

            $blueprint = new Blueprint($request->except('file'));
            $blueprint->created_by = Auth::id();
            $blueprint->save();

            $file = $request->file('file');
            $path = "blueprints/{$blueprint->id}";

            if (! Storage::disk('qnap')->exists($path)) {
                Storage::disk('qnap')->makeDirectory($path);
            }

            $storagePath = Storage::disk('qnap')->put($path, $file);

            if (Storage::disk('qnap')->exists($storagePath)) {
                $blueprint->files()->create([
                    'path' => $storagePath,
                    'upload_by' => Auth::id(),
                    'version' => 1,
                    'state' => 1,
                    'type' => 'PRD',
                    'miniature' => $miniature,
                ]);
            } else {
                DB::rollBack();

                return response()->json("error saving file: {$path}", 500);
            }

            DB::commit();

            $blueprints = Blueprint::with('files', 'created_user')->get();

            return response()->json($blueprints, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }
}
