<?php

namespace App\Http\Controllers;

use App\Models\DesignRequirementArt;
use App\Models\EncoderProductType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;


class ArchaicArtController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = DB::table('V_ARCHAIC_ARTS')
            ->orderBy('code', 'desc')
            ->get();

        return Inertia::render('Applications/ArchaicArts', [
            "arts" => $data,
        ]);
    }

    /**
     * @param $art
     * @return JsonResponse
     */
    public function verify($art): JsonResponse
    {
        $quantity = DesignRequirementArt::where('code', '=', $art)->count();

        if ($quantity > 0) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        try {
            DesignRequirementArt::create([
                'code' => $request->art,
                'archaic_product_code' => $request->product_code
            ]);

            $file = $request->file('file');

            if (!Storage::exists("arts")) {
                Storage::makeDirectory("arts");
            }

            Storage::putFileAs("arts", $file, $request->art . '.pdf');

            $data = DB::table('V_ARCHAIC_ARTS')
                ->orderBy('code', 'desc')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
