<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EditorNotesInvoiceController extends Controller
{
    /**
     * EditorNotesInvoiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:application.editor-nf');
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Applications/EditorNotesInvoices');
    }

    /**
     * get_notes
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function get_notes(Request $request): JsonResponse
    {
        try {
            $notes = DB::connection('MAX')
                ->table('Invoice_Master')
                ->where('INVCE_31', '=', str_pad($request->invoice, 8, "0",STR_PAD_LEFT))
                ->select('COMNT1_31', 'COMNT2_31', 'COMNT3_31', 'CUSTPO_31')->first();

            if ($notes) {
                $notes_string = join(' ', [trim($notes->COMNT1_31), trim($notes->COMNT2_31), trim($notes->COMNT3_31)]);

                return response()->json([
                    'notes' => trim($notes_string),
                    'oc' => trim($notes->CUSTPO_31),
                ], 200);
            } else {
                return response()->json('data empty', 422);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            $notes = str_split(strtoupper($request->notes), 30);

            DB::connection('MAX')
                ->table('Invoice_Master')
                ->where('INVCE_31', '=', str_pad($request->invoice, 8, "0",STR_PAD_LEFT))
                ->update([
                    'COMNT1_31' => array_key_exists(0, $notes) ? $notes[0] : ' ',
                    'COMNT2_31' => array_key_exists(1, $notes) ? $notes[1] : ' ',
                    'COMNT3_31' => array_key_exists(2, $notes) ? $notes[2] : ' ',
                    'CUSTPO_31' => $request->oc ?? ' ',
                    'ModifiedBy' => auth()->user()->username,
                ]);

            DB::connection('MAX')->commit();
            return response()->json('success', 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            return response()->json($e->getMessage());
        }
    }
}
