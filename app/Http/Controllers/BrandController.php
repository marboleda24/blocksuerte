<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Brand::create([
                'name' => strtoupper($request->name),
                'customer_code' => $request->customer_code ?? null,
                'details' => 'N/A',
                'type' => $request->type,
                'created_by' => auth()->user()->id,
                'state' => true,
            ]);

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
