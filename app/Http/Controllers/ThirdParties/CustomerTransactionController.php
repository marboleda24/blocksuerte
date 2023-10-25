<?php

namespace App\Http\Controllers\ThirdParties;

use App\Http\Controllers\Controller;
use App\Models\LogModificationCustomer;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CustomerTransactionController extends Controller
{
    /**
     * CustomerTransactionController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.customers');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function business_name(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'NAME_23' => strtoupper($request->business_name),
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-{${Auth::user()->name}}" ,
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'nombres' => $request->business_name,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'business_name',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->business_name, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function comercial_name(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'ADDR3_23' => strtoupper($request->comercial_name),
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'razon_comercial' => strlen($request->comercial_name) > 0 ? strtoupper($request->comercial_name) : NULL,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'comercial_name',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->comercial_name, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function contact(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'CNTCT_23' => strtoupper($request->contact),
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'contacto_1' => strtoupper($request->contact),
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'contact',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->contact, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function phone1(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'PHONE_23' => $request->phone1,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'telefono_1' => $request->phone1,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'phone1',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->phone1, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function phone2(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'TELEX_23' => $request->phone2,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'telefono_2' => $request->phone2,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'phone2',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->phone2, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function cellphone(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'FAXNO_23' => $request->cellphone ?? '',
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'celular' => $request->cellphone ?? '',
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'cellphone',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->cellphone, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function contact_email(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'EMAIL1_23' => $request->contact_email,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'mail' => $request->contact_email,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'contact_email',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->contact_email, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function billing_email(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'EMAIL2_23' => $request->billing_email,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'electronic-billing-email',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->billing_email, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function copy_mails(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['CorreosCopia' => $request->copy_emails]
                );

            DB::connection('MAX')->commit();

            return response()->json($request->copy_emails, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function address1(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'ADDR1_23' => $request->address1,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'direccion' => $request->address1,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'address1',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->address1, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function address2(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'ADDR2_23' => $request->address2 ?? '',
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'direccion' => $request->address2 ?? '',
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'address2',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->address2, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function currency(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'CURR_23' => $request->currency,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'currency',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->currency, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function customer_type(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'CUSTYP_23' => $request->customer_type,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            if ($request->customer_type !== 'PN' && $request->customer_type !== 'EX') {
                DB::connection('DMS')
                    ->table('terceros')
                    ->update([
                        'tieneRUT' => 1,
                    ]);
            }

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'customer-type',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->customer_type, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function gravado(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'TAXABL_23' => $request->gravado,
                    'TXCDE1_23' => $request->gravado === 'Y' ? 'IVA-V19' : '',
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'gravado',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->gravado, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function payment_term(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'TERMS_23' => $request->paid_term,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'condicion' => $request->paid_term,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'payment-term',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->paid_term, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function discount_rate(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'DSCRTE_23' => $request->discount,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' => "EVPIU-".Auth::user()->username
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'discount-rate',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->discount, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function seller(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'SLSREP_23' => $request->seller,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' =>"EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'vendedor' => $request->seller,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'seller',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->seller, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function electronic_invoicing_manager(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['ResponsableFE' => $request->electronic_invoicing_manager]
                );

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'electronic-invoicing-manager',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->electronic_invoicing_manager, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function electronic_invoicing_phone(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['TelFE' => $request->electronic_invoicing_phone]
                );

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'electronic-invoicing-phone',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->electronic_invoicing_phone, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function rut_delivered(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['RUT' => $request->rut]
                );

            DB::connection('DMS')
                ->table('terceros')
                ->update([
                    'tieneRUT' => $request->rut,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'rut-delivered',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->rut, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function responsible_taxes(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['ResponsableIVA' => (bool)$request->responsible_taxes]
                );

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'responsible-taxes',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();

            return response()->json($request->rut, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function great_contributor(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['GranContr' => $request->great_contributor]
                );

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'gran_contribuyente' => $request->great_contributor,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'great-contributor',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->great_contributor, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function economic_activity(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->updateOrInsert(
                    ['CUSTID_23' => $request->customer_code],
                    ['ActividadPrincipal' => $request->economic_activity]
                );

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'codigoActividadEconomica' => $request->economic_activity,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'economic-activity',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json($request->economic_activity, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function location(Request $request)
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('customer_master')
                ->where('CUSTID_23', '=', $request->customer_code)
                ->update([
                    'CNTRY_23' => $request->country,
                    'STATE_23' => $request->department,
                    'CITY_23' => $request->city,
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'ModifiedBy' =>"EVPIU-".Auth::user()->username
                ]);

            DB::connection('DMS')
                ->table('terceros')
                ->where('codigo_alterno', '=', $request->customer_code)
                ->update([
                    'y_pais' => $request->country_code,
                    'pais' => $request->country,
                    'y_dpto' => $request->department_code,
                    'y_ciudad' => $request->department_code,
                    'ciudad' => $request->city,
                ]);

            LogModificationCustomer::create([
                'modified_by' => auth()->user()->id,
                'field' => 'location',
                'customer_code' => $request->customer_code,
                'justify' => $request->justify,
            ]);

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            return response()->json([
                'country' => $request->country,
                'department' => $request->department,
                'city' => $request->city,
            ], 200);
        } catch (Exception $e) {
            DB::connection('DMS')->rollBack();
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function countries()
    {
        $countries = DB::connection('DMS')
            ->table('y_paises')
            ->orderBy('descripcion')
            ->get();

        return response()->json($countries, 200);
    }

    /**
     * @param $country_name
     * @return JsonResponse
     */
    public function departments($country_name)
    {
        $country = DB::connection('DMS')
            ->table('y_paises')
            ->where('descripcion', '=', $country_name)
            ->first();

        $departments = DB::connection('DMS')
            ->table('y_departamentos')
            ->where('pais', '=', $country->pais)
            ->orderBy('descripcion')
            ->get();

        return response()->json($departments, 200);
    }

    /**
     * @param $country_name
     * @param $department_name
     * @return JsonResponse
     */
    public function cities($country_name, $department_name)
    {
        $country = DB::connection('DMS')
            ->table('y_paises')
            ->where('descripcion', '=', $country_name)
            ->first();

        $department = DB::connection('DMS')
            ->table('y_departamentos')
            ->where('pais', '=', $country->pais)
            ->where('descripcion', '=', $department_name)
            ->first();

        $cities = DB::connection('DMS')
            ->table('y_ciudades')
            ->where('pais', '=', $country->pais)
            ->where('departamento', '=', $department->departamento)
            ->orderBy('descripcion')
            ->get();

        return response()->json($cities, 200);
    }
}
