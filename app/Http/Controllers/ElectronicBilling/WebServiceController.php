<?php

namespace App\Http\Controllers\ElectronicBilling;

use App\Http\Controllers\Controller;
use App\Jobs\SendDocumentDianQueue;
use App\Traits\ElectronicBillingTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpseclib3\System\SSH\Agent\Identity;

class WebServiceController extends Controller
{
    use  ElectronicBillingTrait;

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function send_document_api(Request $request, $entity): JsonResponse
    {
        try {
            foreach ($request->invoices as $invoice) {

                $job = (new SendDocumentDianQueue($invoice, 'invoice', Auth::id(), $entity))
                    ->delay(now()->addSeconds(10));
                dispatch($job);
            }

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $entity
     * @param $invoice
     * @return array
     * @throws GuzzleException
     * @throws \Throwable
     */
    public function invoice_json($entity, $invoice){
        return $this->sendInvoiceExportDian($invoice, $entity);
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function credit_note(Request $request, $entity): JsonResponse
    {
        try {
            foreach ($request->documents as $document) {
                $job = (new SendDocumentDianQueue($document, 'credit_note', Auth::id(), $entity))
                    ->delay(now()->addSeconds(10));
                dispatch($job);
            }

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function debit_note(Request $request, $entity): JsonResponse
    {
        try {
            foreach ($request->documents as $document) {
                $job = (new SendDocumentDianQueue($document, 'debit_note', Auth::id(), $entity))
                    ->delay(now()->addSeconds(10));
                dispatch($job);
            }

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function invoice_export(Request $request, $entity): JsonResponse
    {
        try {
            foreach ($request->invoices as $invoice) {
                $job = (new SendDocumentDianQueue($invoice, 'invoice_export', Auth::id(), $entity))
                    ->delay(now()->addSeconds(10));
                dispatch($job);
            }

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $entity
     * @return JsonResponse
     */
    public function credit_note_export(Request $request, $entity): JsonResponse
    {
        try {
            foreach ($request->documents as $document) {
                $job = (new SendDocumentDianQueue($document, 'credit_note_export', Auth::id(), $entity))
                    ->delay(now()->addSeconds(10));
                dispatch($job);
            }

            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
