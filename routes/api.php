<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\SupplierPurchaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::middleware('auth:sanctum')->prefix('supplier-purchases')->group(function () {
    Route::get('test-auth', function (){
        return response()->json('success', 200);
    });
    Route::post('import', [SupplierPurchaseController::class, 'import']);

    Route::get('exist-document/{cufe}', function ($cufe){
        $exits_document = DB::table('supplier_purchases')
            ->whereJsonContains('document_information', $cufe)
            ->count() > 0;

        if ($exits_document > 0) {
            return response()->json(true);
        }else {
            return response()->json(false);
        }
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

