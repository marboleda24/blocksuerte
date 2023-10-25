<?php

namespace App\Http\Controllers\ThirdParties;

use App\Http\Controllers\Controller;
use App\Models\CustomerFile;
use App\Models\CustomerMax;
use App\Models\HeaderOrder;
use App\Models\LocalCustomer;
use App\Models\LogModificationCustomer;
use App\Models\MAXInvoice;
use App\Models\PartMaster;
use App\Models\RequestInvoice;
use App\Models\User;
use App\Models\Workplace;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CustomerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:application.third-parties.customers', [
            'only' => [
                'index', 'show', 'update_info',
            ],
        ]);

        $this->middleware('permission:application.third-parties.customers.create', [
            'only' => [
                'create', 'store', 'wizard', 'store_new_customer', 'update_info',
            ],
        ]);

        $this->middleware('permission:application.inventory', [
            'only' => [
                'inventory', 'enable_product',
            ],
        ]);
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {

        $data = DB::connection('MAX')
            ->table('CIEV_V_ClientesMAXDMS')
            ->select('CodigoMAX', 'CodigoDMS', 'NITMAX', 'NombreMAX', 'EstadoMAX', 'VENDEDORMAX')
            ->get();

        return Inertia::render('Applications/ThirdParties/Customers/Index', [
            'customers' => $data,
        ]);
    }

    /**
     * @param $customer_code
     * @return Response
     */
    public function show($customer_code): Response
    {
        $data = CustomerMax::with('files')
            ->find($customer_code);

        $orders_count = HeaderOrder::where('customer_code', '=', $customer_code)
            ->count();

        $logs = LogModificationCustomer::with('user')
            ->where('customer_code', '=', $customer_code)
            ->get();

        return Inertia::render('Applications/ThirdParties/Customers/Show/Index', [
            'customer' => $data,
            'orders_count' => $orders_count,
            'logs' => $logs,
        ]);
    }

    /**
     * GetProvinces
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function GetProvinces(Request $request): JsonResponse
    {
        try {
            $country_name = DB::connection('DMS')->table('y_paises')
                ->where('descripcion', '=', $request->country)
                ->pluck('pais')
                ->first();

            $provincies = DB::connection('DMS')
                ->table('y_departamentos')
                ->orderBy('descripcion', 'asc')
                ->where('pais', '=', $country_name == null ? $request->country : $country_name)
                ->get();

            return response()->json($provincies, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * GetCities
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function GetCities(Request $request): JsonResponse
    {
        try {
            $country = DB::connection('DMS')->table('y_paises')
                ->where('descripcion', '=', $request->country)
                ->pluck('pais')
                ->first();

            $province = DB::connection('DMS')->table('y_departamentos')
                ->where('pais', '=', $country)
                ->where('descripcion', '=', $request->province)
                ->pluck('departamento')
                ->first();

            $cities = DB::connection('DMS')
                ->table('y_ciudades')
                ->orderBy('descripcion', 'asc')
                ->where('pais', '=', $country == null ? $request->country : $country)
                ->where('departamento', '=', $province == null ? $request->province : $province)
                ->get();

            return response()->json($cities, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $document
     * @return JsonResponse
     */
    public function validate_client($document): JsonResponse
    {
        $max_result = DB::connection('MAX')->table('Customer_Master')->where('UDFKEY_23', '=', $document)->count();
        $dms_result = DB::connection('DMS')->table('terceros')->where('nit', '=', $document)->count();
        $lcl_result = LocalCustomer::where('document', '=', $document)->count();

        if ($max_result > 0 || $dms_result > 0 || $lcl_result > 0) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    /**
     * @param $business_name
     * @return JsonResponse
     */
    public function business_name($business_name): JsonResponse
    {
        $max_result = DB::connection('MAX')->table('Customer_Master')->where('NAME_23', 'like', '%' . $business_name . '%')->count();
        $dms_result = DB::connection('DMS')->table('terceros')->where('nombres', 'like', '%' . $business_name . '%')->count();
        $lcl_result = LocalCustomer::where('business_name', 'like', '%' . $business_name . '%')->count();

        if ($max_result > 0 || $dms_result > 0 || $lcl_result > 0) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    /**
     * store customer in local database
     *
     * @param mixed $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            LocalCustomer::create([
                'document_type' => $request['step1']['document_type'],
                'document' => intval($request['step1']['document']),
                'customer_type' => $request['step1']['customer_type'],
                'first_name' => $request['step1']['customer_type'] == 'PN' ? strtoupper($request['step1']['first_name']) : null,
                'second_name' => $request['step1']['customer_type'] == 'PN' ? strtoupper($request['step1']['second_name']) : null,
                'surname' => $request['step1']['customer_type'] == 'PN' ? strtoupper($request['step1']['surname']) : null,
                'second_surname' => $request['step1']['customer_type'] == 'PN' ? strtoupper($request['step1']['second_surname']) : null,
                'business_name' => $request['step1']['customer_type'] == 'PJ' ? strtoupper($request['step1']['business_name']) : null,
                'business_reason' => $request['step1']['business_reason'],
                'country' => $request['step1']['country'],
                'province' => $request['step1']['province'],
                'city' => $request['step1']['city'],
                'address' => $request['step1']['address'],
                'phone' => intval($request['step1']['phone']),
                'cellphone' => intval($request['step1']['cellphone']),
                'email' => strtolower($request['step1']['email']),
                'seller_id' => $request['step2']['seller'],
                'type_legal_entity' => $request['step1']['customer_type'] == 'PJ' ? $request['step2']['type_legal_entity'] : 'PN',
                'rut_file' => boolval($request['step2']['tieneRUT']),
                'main_activity' => $request['step2']['main_activity'],
                'great_contributor' => $request['step2']['great_contributor'] === '1',
                'responsable_iva' => $request['step2']['responsable_iva'] === 'true' ? '1' : '0',
                'gravado' => boolval($request['step2']['gravado']),
                'email_fe' => strtolower($request['step2']['email_fe']),
                'emails_copies_fe' => $request['step2']['emails_copies_fe'],
                'created_by' => auth()->user()->id,
                'state' => '1',
                'type_third' => $request['step1']['type_third'],
            ]);

            $code = intval($request['step1']['document']);

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "customers/{$code}/files";

                    $full_path = storage_path() . "/app/customers/{$code}/files/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::putFileAs("customers/{$code}/files", $file, $filename);

                    if (Storage::exists($storagePath)) {
                        CustomerFile::create([
                            'customer_code' => $code,
                            'name' => $filename,
                            'path' => $storagePath,
                        ]);

                    } else {
                        DB::rollBack();
                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            DB::commit();
            return response()->json('customer created successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * create customer form
     *
     * @return Response
     */
    public function create(): Response
    {
        $countries = DB::connection('DMS')
            ->table('y_paises')
            ->orderBy('descripcion', 'asc')
            ->get();

        $sellers = User::where('occupation', 'vendedor')
            ->orderBy('name', 'asc')
            ->get();

        $main_activities = DB::connection('DMS')
            ->table('Terceros_actividad_economica')
            ->orderBy('codigo', 'asc')
            ->get();

        return Inertia::render('Applications/ThirdParties/Customers/Create', [
            'countries' => $countries,
            'sellers' => $sellers,
            'main_activities' => $main_activities,
        ]);
    }

    /**
     * credit_limit_gestion
     *
     * @return Response
     */
    public function credit_limit_gestion(): Response
    {
        $customers = LocalCustomer::with('seller')->where('state', '1')->get();
        $terms = DB::connection('MAX')->table('Code_Master')->where('CDEKEY_36', '=', 'TERM')->where('DAYS_36', '<>', '')->orderBy('DESC_36', 'asc')->get();
        $array = [];

        foreach ($customers as $customer) {
            $array[] = [
                'id' => $customer->id,
                'document' => $customer->document,
                'name' => $customer->business_name ?? implode(' ', [$customer->first_name, $customer->second_name, $customer->surname, $customer->second_surname]),
                'seller' => $customer->seller->name,
            ];
        }

        return Inertia::render('Applications/ThirdParties/Customers/Gestion', [
            'customers' => $array,
            'terms' => $terms,
        ]);
    }

    /**
     * credit_limit_gestion_show
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function credit_limit_gestion_show(Request $request): JsonResponse
    {
        try {
            $data = LocalCustomer::with('seller', 'country_name', 'province_name', 'city_name', 'createdby')->find($request->id);

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * store_new_customer
     *
     * @param mixed $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function store_new_customer(Request $request): JsonResponse
    {
        DB::beginTransaction();
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();
        try {
            LocalCustomer::find($request->id)->update([
                'credit_limit' => $request->credit_limit,
                'payment_deadline' => $request->term,
                'discount_rate' => $request->discount,
            ]);

            $lc = LocalCustomer::with('seller', 'country_name', 'province_name', 'city_name', 'createdby')
                ->find($request->id);

            $slster = trim(DB::connection('MAX')
                ->table('Sales_Rep_Master')
                ->where('SLSREP_26', '=', $lc->seller->vendor_code)
                ->pluck('SLSTER_26')
                ->first());

            DB::connection('MAX')
                ->table('Customer_Master')
                ->insert([
                    'CUSTID_23' => $lc->document,
                    'SLSREP_23' => $lc->seller->vendor_code,
                    'STATUS_23' => 'R',
                    'CUSTYP_23' => $lc->type_legal_entity,
                    'NAME_23' => $lc->business_name ?? implode(' ', [$lc->surname, $lc->second_surname, $lc->first_name, $lc->second_name]),
                    'ADDR1_23' => strtoupper($lc->address),
                    'ADDR2_23' => ' ',
                    'CITY_23' => $lc->city_name->descripcion,
                    'STATE_23' => $lc->province_name->descripcion,
                    'ZIPCD_23' => ' ',
                    'CNTRY_23' => $lc->country_name->descripcion,
                    'SLSTER_23' => $slster, //$territorio_cliente[0],
                    'CNTCT_23' => ' ',
                    'PHONE_23' => $lc->phone,
                    'EMAIL1_23' => strtolower($lc->email),
                    'EMAIL2_23' => strtolower($lc->email_fe),
                    'TELEX_23' => ' ',
                    'FAXNO_23' => $lc->cellphone === '0' || $lc->cellphone === null ? '' : $lc->cellphone,
                    'TAXABL_23' => $lc->type_third === 'EX' ? 'N' : ($lc->gravado ? 'Y' : 'N'),
                    'TAXNUM_23' => $lc->type_third === 'EX' ? 'EXCENTO' : (!$lc->gravado ? 'EXCENTO' : ' '),
                    'TXCDE1_23' => $lc->type_third === 'EX' ? '' : ($lc->gravado ? 'IVA-V19' : ' '),
                    'TERMS_23' => $lc->payment_deadline,
                    'DSCRTE_23' => $lc->discount_rate,
                    'SHPVIA_23' => $slster == 'MEDELLIN' ? '02' : ($slster == 'ENTREGA DIRECTA' ? '03' : '01'),
                    'SLSMTD_23' => '0',
                    'COGMTD_23' => '0',
                    'SLSYTD_23' => '0',
                    'COGYTD_23' => '0',
                    'COGLYR_23' => '0',
                    'UNPORD_23' => '0',
                    'NEWDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'DISCPF_23' => 'B',
                    'ALWBCK_23' => 'N',
                    'CHGDTE_23' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'CURR_23' => $lc->type_third === 'EX' ? 'USD' : 'COP',
                    'COMMIS_23' => '0',
                    'UDFKEY_23' => $lc->document . '-' . $this->calculate_dv($lc->document),
                    'CreatedBy' => auth()->user()->username,
                    'CreationDate' => Carbon::now()->format('Y-m-d\Th:m:s.v'),
                    'VATSUSP_23' => 'N',
                    'COMNT1_23' => ' ',
                    'COMNT2_23' => ' ',
                    'TXCDE2_23' => ' ',
                    'TXCDE3_23' => ' ',
                    'CLIMIT_23' => $lc->credit_limit,
                    'STMNTS_23' => ' ',
                    'FINCHG_23' => ' ',
                    'XURR_23' => ' ',
                    'FOB_23' => ' ',
                    'SLSLYR_23' => ' ',
                    'TAXPRV_23' => ' ',
                    'ADDR3_23' => strtoupper($lc->bussines_reason),
                    'ADDR4_23' => ' ',
                    'ADDR5_23' => ' ',
                    'ADDR6_23' => ' ',
                    'MCOMP_23' => ' ',
                    'MSITE_23' => ' ',
                    'UDFREF_23' => ' ',
                    'SHPCDE_23' => ' ',
                    'SHPTHRU_23' => ' ',
                ]);

            DB::connection('MAX')
                ->table('Customer_Master_Ext')
                ->insert([
                    'CUSTID_23' => $lc->document,
                    'GRUPOECON' => '',
                    'TipoIdent' => $lc->document_type,
                    'GranContr' => $lc->great_contributor,
                    'ActividadPrincipal' => $lc->main_activity,
                    'RUT' => $lc->rut_file ? '1' : '0',
                    'ResponsableFE' => '',
                    'telFE' => '',
                    'CorreosCopia' => preg_replace('/[ ,]+/', ';', $lc->emails_copies_fe), //pending
                    'ResponsableIVA' => $lc->responsable_iva,
                    'CiudadExterior' => '',
                ]);

            $iddtt = 10;
            if ($lc->customer_type === 'PN') {
                $iddtt = 14;
            } elseif ($lc->customer_type === 'PJ' && $lc->great_contributor === '1') {
                $iddtt = 11;
            } elseif ($lc->customer_type === 'PJ' || $lc->responsable_iva === '1' ||$lc->customer_type === 'PJ' && $lc->document_type === 'N') {
                $iddtt = 12;
            }

            DB::connection('DMS')
                ->table('terceros')
                ->insert([
                    'nit' => $lc->document,
                    'digito' => $this->calculate_dv($lc->document),
                    'nombres' => $lc->business_name ?? implode(' ', [$lc->surname, $lc->second_surname, $lc->first_name, $lc->second_name]),
                    'direccion' => strtoupper($lc->address),
                    'ciudad' => $lc->city_name->descripcion,
                    'telefono_1' => $lc->phone,
                    'telefono_2' => '',
                    'fax' => null,
                    'tipo_identificacion' => $lc->type_third === 'EX' ? 'E3' : $lc->document_type, // pendiente por que los valores deben ser equivalentes en max
                    'pais' => $lc->country_name->descripcion,
                    'gran_contribuyente' => '0',
                    'autoretenedor' => '0',
                    'bloqueo' => '0',
                    'concepto_1' => '1',
                    'concepto_2' => $this->province_code($lc->province),
                    'concepto_3' => '15',
                    'concepto_4' => $lc->type_legal_entity == 'CI' ? 2 : ($lc->type_legal_entity == 'ZF' ? 4 : 1),
                    'mail' => strtolower($lc->email),
                    'pos_num' => $lc->business_name ? strlen($lc->business_name) : strlen(implode(' ', [$lc->surname, $lc->second_surname])),
                    'regimen' => $lc->customer_type === 'PJ' || $lc->responsable_iva ===1 ? 'C' : 'S',
                    'cupo_credito' => $lc->credit_limit,
                    'nit_real' => $lc->document,
                    'condicion' => $lc->payment_deadline == '00' ? '10' : $lc->payment_deadline,
                    'vendedor' => $lc->seller->vendor_code,
                    'contacto_1' => '' ?? null,
                    'fecha_creacion' => Carbon::now(),
                    'descuento_fijo' => $lc->discount_rate,
                    'centro_fijo' => '0',
                    'y_dpto' => $lc->province,
                    'y_ciudad' => $lc->city,
                    'celular' => $lc->cellphone,
                    'razon_comercial' => strtoupper($lc->bussines_reason),
                    'y_pais' => $lc->country,
                    'codigo_alterno' => $lc->document,
                    'usuario' => auth()->user()->username,
                    'sincronizado' => 'N',
                    'id_definicion_tributaria_tipo' => $lc->type_third === 'EX' ? 10 : $iddtt,
                    'tieneRUT' => $lc->rut_file === 1 || $lc->main_activity !== null ? 1 : 0,
                    'codigoPostal' => '' ?? null,
                    'codigoActividadEconomica' => $lc->main_activity,
                ]);

            if ($lc->business_name) {
                DB::connection('DMS')
                    ->table('terceros_nombres')
                    ->insert([
                        'nit' => $lc->document,
                        'primer_apellido' => $lc->surname,
                        'segundo_apellido' => $lc->second_surname,
                        'primer_nombre' => $lc->first_name,
                        'segundo_nombre' => $lc->second_name,
                    ]);
            }

            DB::commit();
            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            $lc->state = '2';
            $lc->save();

            $customers = LocalCustomer::with('seller')->where('state', '1')->get();
            $array = [];

            foreach ($customers as $customer) {
                $array[] = [
                    'id' => $customer->id,
                    'document' => $customer->document,
                    'name' => $customer->business_name ?? implode(' ', [$customer->first_name, $customer->second_name, $customer->surname, $customer->second_surname]),
                    'seller' => $customer->seller->name,
                ];
            }

            return response()->json($array, 200);
        } catch (Exception $e) {
            DB::rollBack();
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * calculate_dv
     *
     * @param mixed $nit
     * @return int
     */
    private function calculate_dv(mixed $nit): int
    {
        $arr = [
            1 => 3,
            4 => 17,
            7 => 29,
            10 => 43,
            13 => 59,
            2 => 7,
            5 => 19,
            8 => 37,
            11 => 47,
            14 => 67,
            3 => 13,
            6 => 23,
            9 => 41,
            12 => 53,
            15 => 71,
        ];
        $x = 0;
        $z = strlen($nit);

        for ($i = 0; $i < $z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y * $arr[$z - $i]);
        }

        $y = $x % 11;

        if ($y > 1) {
            $dv = 11 - $y;
        } else {
            $dv = $y;
        }

        return $dv;
    }

    /**
     * @param $province
     * @return string
     */
    private function province_code($province)
    {
        return match ($province) {
            '05' => '1',
            '11', '15', '25' => '11',
            '08', '13', '20', '23', '44', '47', '70' => '12',
            '18', '41', '50', '81', '85', '86', '91', '94', '95', '97', '99' => '13',
            '17', '63', '66', '73' => '14',
            '54', '68' => '15',
            '19', '27', '52', '76' => '16',
            default => '17'
        };
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function update_info(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        DB::connection('DMS')->beginTransaction();

        try {
            switch ($request->field_name) {
                case 'location':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update([
                            'CNTRY_23' => $request->field_value['country'],
                            'STATE_23' => $request->field_value['department'],
                            'CITY_23' => $request->field_value['city'],
                        ]);

                    $country = $this->get_code_location('country', $request->field_value['country']);
                    $province = $this->get_code_location('province', $request->field_value['department']);
                    $city = $this->get_code_location('city', $request->field_value['city']);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update([
                            'pais' => $request->field_value['country'],
                            'y_pais' => $country,
                            'y_dpto' => $province,
                            'ciudad' => $request->field_value['city'],
                            'y_ciudad' => $city,
                        ]);

                    LogModificationCustomer::create([
                        'field' => 'Ubicacion',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'address1':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update([
                            'ADDR1_23' => $request->field_value,
                        ]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['direccion' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Direccion 1',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'address2':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['ADDR2_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Direccion 2',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'currency':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['CURR_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Moneda',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'customer_type':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['CUSTYP_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Tipo Cliente',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'contact':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['CNTCT_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['contacto_1' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Contacto',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'phone1':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['PHONE_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['telefono_1' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Telefono 1',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'phone2':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['TELEX_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['telefono_2' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Telefono 2',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'cellphone':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['FAXNO_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['celular' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Celular',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'contact_email':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['EMAIL1_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['mail' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Email de contacto',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'billing_email':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['EMAIL2_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Email de facturacion electronica',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'paid_term':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['TERMS_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['condicion' => $request->field_value == '00' ? '10' : $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Condicion de pago',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'discount':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['DSCRTE_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Descuento',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'seller':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['SLSREP_23' => $request->field_value]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['vendedor' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'Vendedor',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'copy_emails':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code], [
                            'CorreosCopia' => $request->field_value,
                        ]);

                    LogModificationCustomer::create([
                        'field' => 'Correos copia',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'rut':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code], [
                            'RUT' => $request->field_value === '1' ? true : false,
                        ]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->where('codigo_alterno', '=', $request->customer_code)
                        ->update(['tieneRUT' => $request->field_value === '1' ? 1 : 0,
                        ]);

                    LogModificationCustomer::create([
                        'field' => 'RUT',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'great_contributor':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code],
                            ['GranContr' => $request->field_value === '1' ? true : false]);

                    DB::connection('DMS')
                        ->table('terceros')
                        ->updateOrInsert(['codigo_alterno' => $request->customer_code],
                            ['gran_contribuyente' => $request->field_value === '1' ? true : false,
                            ]);

                    LogModificationCustomer::create([
                        'field' => 'Gran contribuyente',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'responsible_iva':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code], [
                            'ResponsableIVA' => $request->field_value === '1' ? true : false,
                        ]);

                    LogModificationCustomer::create([
                        'field' => 'Responsable IVA',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'responsible_electronic_billing':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code],
                            ['ResponsableFE' => $request->field_value,
                            ]);

                    LogModificationCustomer::create([
                        'field' => 'Responsable facturacion electronica',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'phone_electronic_billing':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(
                            ['CUSTID_23' => $request->customer_code],
                            ['telFE' => $request->field_value]
                        );

                    LogModificationCustomer::create([
                        'field' => 'Telefono facturacion electronica',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'code_city_ext':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code],
                            ['CiudadExterior' => $request->field_value,
                            ]);

                    LogModificationCustomer::create([
                        'field' => 'Ciudad exterior',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'economic_group':
                    DB::connection('MAX')
                        ->table('customer_master_ext')
                        ->updateOrInsert(['CUSTID_23' => $request->customer_code],
                            [
                                'GRUPOECON' => $request->field_value,
                            ]);

                    LogModificationCustomer::create([
                        'field' => 'Grupo economico',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);

                    break;

                case 'grabado':
                    DB::connection('MAX')
                        ->table('customer_master')
                        ->where('CUSTID_23', '=', $request->customer_code)
                        ->update(['TAXABL_23' => $request->field_value]);

                    LogModificationCustomer::create([
                        'field' => 'gravado',
                        'justify' => $request->justify,
                        'modified_by' => auth()->user()->id,
                    ]);
            }

            DB::connection('MAX')->commit();
            DB::connection('DMS')->commit();

            $data = CustomerMax::find($request->customer_code);

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();
            DB::connection('DMS')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param $type
     * @param $name
     * @return mixed
     */
    private function get_code_location($type, $name)
    {
        switch ($type) {
            case 'country':
                return DB::connection('DMS')->table('y_paises')
                    ->where('descripcion', '=', $name)->pluck('pais')->first();

            case 'province':
                return DB::connection('DMS')->table('y_departamentos')
                    ->where('descripcion', '=', $name)->pluck('departamento')->first();

            case 'city':
                return DB::connection('DMS')->table('y_ciudades')
                    ->where('descripcion', '=', $name)->pluck('ciudad')->first();
        }
    }

    /**
     * @param $invoice
     * @return JsonResponse
     */
    public function invoice_detail($invoice): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasDetalladas')
                ->where('Factura', '=', $invoice)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function inventory(): Response
    {
        return Inertia::render('Applications/ThirdParties/Inventory');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function query_inventory(Request $request): JsonResponse
    {
        if ($request->type == 'inventory') {
            $q = (string)$request->q;
            try {
                $data = PartMaster::with('part_sales')
                    ->where(function ($query) use ($q) {
                        $query->where('PMDES1_01', 'LIKE', '%' . $q . '%')
                            ->orWhere('PRTNUM_01', 'like', '%' . $q . '%');
                    })->where(function ($query) {
                        $query->where('ONHAND_01', '>', 0)
                            ->orWhere('NONNET_01', '>', 0);
                    })->get();

                return response()->json($data, 200);
            } catch (Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        } elseif ($request->type == 'products') {
            $q = (string)$request->q;

            try {
                $data = PartMaster::where(function ($query) use ($q) {
                    $query->where('PMDES1_01', 'LIKE', '%' . $q . '%')
                        ->orWhere('PRTNUM_01', 'like', '%' . $q . '%');
                })->where('PRTNUM_01', 'NOT LIKE', '%3500')
                    ->take(1000)
                    ->get();

                return response()->json($data, 200);
            } catch (Exception $e) {
                return response()->json($e->getMessage(), 500);
            }
        }
    }

    /**
     * @param $reference
     * @return JsonResponse
     */
    public function available_amount($reference): JsonResponse
    {
        try {
            $data = DB::connection('MAX')->table('Part_Stock')
                ->join('Part_Master', 'Part_Stock.PRTNUM_06', '=', 'Part_Master.PRTNUM_01')
                ->where('PRTNUM_06', '=', $reference)
                ->where('STK_06', '<>', 'PPEYV')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function enable_product(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Part_Master')
                ->where('PRTNUM_01', '=', $request->reference)
                ->update([
                    'OBSDTE_01' => null,
                    'STAENG_01' => '2',
                    'ModifiedBy' => auth()->user()->username,

                ]);

            DB::connection('MAX')->commit();

            return response()->json('product enabled successfully', 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function wizard(): Response
    {
        return Inertia::render('Applications/ThirdParties/Customers/Wizard');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_invoices(Request $request): JsonResponse
    {
        try {
            $date = explode(' - ', $request->date);

            $data = MAXInvoice::with('details')
                ->where('TIPODOC', '=', 'CU')
                ->where('CLIENTE', '=', $request->customer)
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_credit_notes(Request $request): JsonResponse
    {
        try {
            $date = explode(' - ', $request->date);

            $data = MAXInvoice::with('details')
                ->where('TIPODOC', '=', 'CR')
                ->where('CLIENTE', '=', $request->customer)
                ->whereBetween('FECHA', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_open_ov(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_OVAbiertas')
                ->select('OV', 'FECHA_OV', 'OC')
                ->where('COD_CLIENTE', '=', $request->customer)
                ->groupBy('OV', 'FECHA_OV', 'OC')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_close_ov(Request $request): JsonResponse
    {
        try {
            $date = explode(' - ', $request->date);

            $data = DB::connection('MAX')
                ->table('CIEV_V_OVCerradas')
                ->where('COD_CLIENTE', '=', $request->customer)
                ->whereBetween('FECHA_OV', [Carbon::parse($date[0])->format('Y-m-d 00:00:00'), Carbon::parse($date[1])->format('Y-m-d 23:59:59')])
                ->select('OV', 'FECHA_OV', 'OC')
                ->groupBy('OV', 'FECHA_OV', 'OC')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function detail_ov(Request $request): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('CIEV_V_OVAbiertas')
                ->where('COD_CLIENTE', '=', $request->customer)
                ->where('OV', '=', $request->ov)
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get list customer types
     *
     * @return JsonResponse
     */
    public function customer_types(): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('Customer_Types')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * get list paid terms
     *
     * @return JsonResponse
     */
    public function paid_terms(): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('Code_Master')
                ->where('CDEKEY_36', '=', 'TERM')
                ->where('DAYS_36', '<>', '')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * sellers list
     *
     * @return JsonResponse
     */
    public function sellers(): JsonResponse
    {
        try {
            $data = DB::connection('MAX')
                ->table('Sales_Rep_Master')
                ->where('UDFKEY_26', '<>', 'RETIRADO')
                ->orderBy('SLSNME_26', 'asc')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function economic_groups(): JsonResponse
    {
        try {
            $data = DB::connection('DMS')
                ->table('terceros_actividad_economica')
                ->orderBy('descripcion', 'asc')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function change_state_customer(Request $request): JsonResponse
    {
        DB::connection('MAX')->beginTransaction();
        try {
            DB::connection('MAX')
                ->table('Customer_Master')
                ->where('CUSTID_23', '=', $request->code)
                ->update([
                    'STATUS_23' => $request->state === 'active' ? 'R' : 'H',
                ]);

            DB::connection('MAX')->commit();

            $data = CustomerMax::find($request->code);

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::connection('MAX')->rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**_________________________________________
     * PROCESOS           ->         process_status
     *
     * CALIDAD                         = 1
     * RECHAZADO CALIDAD               = 2
     * BODEGA                          = 3
     * ENVIAR A CALIDAD DESDE BODEGA   = 1
     * RECHAZAR DESDE BODEGA           = 0
     * FINALIZADO BODEGA               = 4
     * CARTERA                         = 5
     * _____________________________________________*/

    /**
     * ESTADOS    ->     status    =    accion a tomar segun el reclamo
     * ______________________________________________________*
     * 1    = cambio de producto                                *
     * 2    = reproceso                                         *
     * 3    = nota crédito                                      *
     * 4    = fabricación                                       *
     * 5    = reproceso - nota crédito                          *
     * 6    = fabricación - nota crédito                        *
     * ______________________________________________________*
     */

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save_request_invoice(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $req = RequestInvoice::create([
                'id_reason' => $request->id_reason,
                'comments' => $request->comments,
                'new_invoice' => $request->new_invoice,
                'process_status' => $request->state === '3' ? '3' : '1',
                'state' => $request->state,
                'invoice' => $request->invoice,
                'created_by' => auth()->user()->id,
            ]);

            foreach ($request->selected_items as $item) {
                $req->items()->create([
                    'item' => $item['Item'],
                    'product_code' => $item['CodigoProducto'],
                    'quantity' => array_key_exists('quantity', $item) && $item['quantity'] > 0 ? $item['quantity'] : $item['Cantidad'],
                ]);
            }

            foreach ($request->uploadedFiles as $file) {
                $file = $file['dataURL'];

                if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                    $file = substr($file, strpos($file, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        throw new Exception('invalid image type');
                    }

                    $file = str_replace(' ', '+', $file);
                    $file = base64_decode($file);

                    if ($file === false) {
                        throw new Exception('base64_decode failed');
                    }
                } else {
                    throw new Exception('did not match data URI with image data');
                }

                $filename = uniqid(rand(10000, 99999), false) . ".{$type}";
                $path = 'request/invoices/' . $req->id;
                $fullpath = storage_path() . '/app/request/invoices/' . $req->id . '/' . $filename;

                if (!Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path);
                }

                //Storage::disk('local')->put($path.$filename, $file);
                Storage::disk('local')->put('request/invoices/' . $req->id . '/' . $filename, $file);

                if (file_exists($fullpath)) {
                    $req->update([
                        'file' => $filename,
                    ]);
                } else {
                    DB::rollBack();

                    return response()->json('error saving files ' . $fullpath, 500);
                }
            }

            foreach ($request->uploadedApproved as $file) {
                $file = $file['dataURL'];

                if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                    $file = substr($file, strpos($file, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        throw new Exception('invalid image type');
                    }

                    $file = str_replace(' ', '+', $file);
                    $file = base64_decode($file);

                    if ($file === false) {
                        throw new Exception('base64_decode failed');
                    }
                } else {
                    throw new Exception('did not match data URI with image data');
                }

                $file_approved = uniqid(rand(10000, 99999), false) . ".{$type}";
                $path = 'request/invoices/' . $req->id;
                $fullpath = storage_path() . '/app/request/invoices/' . $req->id . '/' . $file_approved;

                if (!Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path);
                }

                //Storage::disk('local')->put($path.$file_approved, $file);
                Storage::disk('local')->put('request/invoices/' . $req->id . '/' . $file_approved, $file);

                if (file_exists($fullpath)) {
                    $req->update([
                        'file_approved' => $file_approved,
                    ]);
                } else {
                    DB::rollBack();

                    return response()->json('error saving files ' . $fullpath, 500);
                }
            }

            DB::commit();

            return response()->json('success', 200);
        } catch (Exception $ex) {
            DB::rollBack();

            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function request_invoice_quality(): Response
    {
        $data = RequestInvoice::with('items.detail', 'reason_reprocessings')
            ->where('process_status', '=', '1')
            ->get();

        $name_workplaces = Workplace::all();

        return Inertia::render('Applications/ThirdParties/Customers/RequestInvoice/Quality', [
            'registros' => $data,
            'centro_trabajo' => $name_workplaces,
        ]);
    }

    /**
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function reports_quality()
    {
        $reports = RequestInvoice::join('reason_reprocessings', 'reason_reprocessings.id', '=', 'request_invoices.id_reason')
            ->groupBy('razon')
            ->get([
                DB::raw('count(*) as cantidad'),
                DB::raw('reason_reprocessings.reason as razon'),
            ]);
        $reportsReason = RequestInvoice::join('workplaces', 'workplaces.id', '=', 'request_invoices.id_worksplace')
            ->groupBy('area')
            ->get([
                DB::raw('count(*) as cantidad'),
                DB::raw('workplaces.name_workplaces as area'),
            ]);

        $reportStatus = RequestInvoice::groupBy('state')
            ->get([
                DB::raw('count(*) as cantidad'),
                DB::raw('request_invoices.state as state'),
            ]);

        return response([
            'reports' => $reports,
            'reportsReason' => $reportsReason,
            'reportStatus' => $reportStatus,
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save_request_invoice_quality(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $req = RequestInvoice::find($request->id);
            $req->update($request->all());
            $req->items()->delete();
            $req->items()->createMany($request->items);
            DB::commit();

            $data = RequestInvoice::all();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send_store(Request $request): JsonResponse
    {
        RequestInvoice::find($request->id)
            ->update([
                'process_status' => '3',
                'updated_at' => Carbon::now(),

            ]);

        $data = RequestInvoice::where('process_status', '1')->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refuse_request_invoice(Request $request): JsonResponse
    {
        try {
            RequestInvoice::find($request->id)
                ->update([
                    'process_status' => '2',
                    'justify' => $request->justify,
                ]);

            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '1')->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function request_invoice_store(Request $request): Response
    {
        $name_workplaces = Workplace::all();
        $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
            ->where('process_status', '=', '3')
            ->get();

        return Inertia::render('Applications/ThirdParties/Customers/RequestInvoice/Store', [
            'data' => $data,
            'name_workplaces' => $name_workplaces,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function save_request_invoice_store(Request $request): JsonResponse
    {
        try {
            RequestInvoice::find($request->id)
                ->update([
                    'id_worksplace' => $request->id_worksplace,
                    'observations' => $request->observations,
                ]);

            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            return response()->json($data, 200);
        } catch (Exception $ex) {
            DB::rollBack();

            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send_quality(Request $request): JsonResponse
    {
        try {
            RequestInvoice::find($request->id)
                ->update([
                    'process_status' => '1',
                    'justify_send_store' => $request->justify_send_store,
                ]);

            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public function vendor_request(): Response
    {
        $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
            ->where('created_by', auth()->user()->id)
            ->get();

        return Inertia::render('Applications/ThirdParties/Customers/RequestInvoice/Vendor', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refuse_request_invoice_store(Request $request): JsonResponse
    {
        try {
            RequestInvoice::find($request->id)
                ->update([
                    'process_status' => '0',
                    'justify_refuse_store' => $request->justify_refuse_store,
                ]);

            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function new_order(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $invoice_request = RequestInvoice::with('items')->find($request->id);

            $max_order_num = DB::connection('MAX')
                    ->table('SO_Master')
                    ->whereIn('STYPE_27', ['CU', 'CR'])
                    ->max('ORDNUM_27') + 1;

            $ov = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas')
                ->where('NUMERO', '=', $invoice_request->invoice)
                ->pluck('OV');

            $so_master = DB::connection('MAX')
                ->table('SO_Master')
                ->where('ORDNUM_27', '=', $ov)
                ->first();

            $so_detail = DB::connection('MAX')
                ->table('SO_Detail')
                ->where('ORDNUM_28', '=', $ov)
                ->get();

            DB::connection('MAX')
                ->table('SO_Master')
                ->insert([
                    'ORDNUM_27' => $max_order_num,
                    'CUSTID_27' => $so_master->CUSTID_27,
                    'GLXREF_27' => '41209505',
                    'STYPE_27' => 'CU',
                    'STATUS_27' => '3',
                    'CUSTPO_27' => $so_master->CUSTPO_27,
                    'ORDID_27' => $so_master->ORDID_27,
                    'ORDDTE_27' => $so_master->ORDDTE_27,
                    'FILL01A_27' => $so_master->FILL01A_27,
                    'FILL01_27' => $so_master->FILL01_27,
                    'SHPCDE_27' => $so_master->SHPCDE_27,
                    'REP1_27' => $so_master->REP1_27,
                    'SPLIT1_27' => $so_master->SPLIT1_27,
                    'REP2_27' => $so_master->REP2_27,
                    'SPLIT2_27' => $so_master->SPLIT2_27,
                    'REP3_27' => $so_master->REP3_27,
                    'SPLIT3_27' => $so_master->SPLIT3_27,
                    'COMMIS_27' => $so_master->COMMIS_27,
                    'TERMS_27' => $so_master->TERMS_27,
                    'SHPVIA_27' => $so_master->SHPVIA_27,
                    'XURR_27' => $so_master->XURR_27,
                    'FOB_27' => $so_master->FOB_27,
                    'TAXCD1_27' => $so_master->TAXCD1_27,
                    'TAXCD2_27' => $so_master->TAXCD2_27,
                    'TAXCD3_27' => $so_master->TAXCD3_27,
                    'COMNT1_27' => $so_master->COMNT1_27,
                    'COMNT2_27' => $so_master->COMNT2_27,
                    'COMNT3_27' => $so_master->COMNT3_27,
                    'SHPLBL_27' => $so_master->SHPLBL_27,
                    'INVCE_27' => $so_master->INVCE_27,
                    'APPINV_27' => $so_master->APPINV_27,
                    'REASON_27' => $so_master->REASON_27,
                    'NAME_27' => $so_master->NAME_27,
                    'ADDR1_27' => $so_master->ADDR1_27,
                    'ADDR2_27' => $so_master->ADDR2_27,
                    'CITY_27' => $so_master->CITY_27,
                    'STATE_27' => $so_master->STATE_27,
                    'ZIPCD_27' => $so_master->ZIPCD_27,
                    'CNTRY_27' => $so_master->CNTRY_27,
                    'PHONE_27' => $so_master->PHONE_27,
                    'CNTCT_27' => $so_master->CNTCT_27,
                    'TAXPRV_27' => $so_master->TAXPRV_27,
                    'FEDTAX_27' => $so_master->FEDTAX_27,
                    'TAXABL_27' => $so_master->TAXABL_27,
                    'EXCRTE_27' => $so_master->EXCRTE_27,
                    'FIXVAR_27' => $so_master->FIXVAR_27,
                    'CURR_27' => $so_master->CURR_27,
                    'RCLDTE_27' => $so_master->RCLDTE_27,
                    'FILL02_27' => $so_master->FILL02_27,
                    'TTAX_27' => $so_master->TTAX_27, /*empty*/
                    'LNETAX_27' => 'N',
                    'ADDR3_27' => $so_master->ADDR3_27,
                    'ADDR4_27' => $so_master->ADDR4_27,
                    'ADDR5_27' => $so_master->ADDR5_27,
                    'ADDR6_27' => $so_master->ADDR6_27,
                    'MCOMP_27' => $so_master->MCOMP_27,
                    'MSITE_27' => $so_master->MSITE_27,
                    'UDFKEY_27' => '', /*empty*/
                    'UDFREF_27' => '', /*empty*/
                    'SHPTHRU_27' => '', /*empty*/
                    'XDFINT_27' => 0,
                    'XDFFLT_27' => 0,
                    'XDFBOL_27' => '', /*empty*/
                    'XDFDTE_27' => null, /*empty*/
                    'XDFTXT_27' => '', /*empty*/
                    'FILLER_27' => '', /*empty*/
                    'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                    'CreationDate' => Carbon::now(),
                    'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                    'ModificationDate' => Carbon::now(),
                    'BILLCDE_27' => '', /*empty*/
                ]);

            foreach ($so_detail as $detail) {
                if ($invoice_request->items->where('item', '=', trim("{$detail->LINNUM_28}{$detail->DELNUM_28}"))->count() > 0) {
                    $item = $invoice_request->items->where('item', '=', trim("{$detail->LINNUM_28}{$detail->DELNUM_28}"))->first();

                    DB::connection('MAX')
                        ->table('SO_Detail')
                        ->insert([
                            'ORDNUM_28' => $max_order_num,
                            'LINNUM_28' => $detail->LINNUM_28,
                            'DELNUM_28' => $detail->DELNUM_28,
                            'STATUS_28' => '3',
                            'CUSTID_28' => $detail->CUSTID_28,
                            'PRTNUM_28' => $detail->PRTNUM_28,
                            'EDILIN_28' => '', /*empty*/
                            'TAXABL_28' => $detail->TAXABL_28,
                            'GLXREF_28' => 61209505,
                            'CURDUE_28' => $detail->CURDUE_28,
                            'QTLINE_28' => '', /*empty*/
                            'ORGDUE_28' => $detail->ORGDUE_28,
                            'QTDEL_28' => '', /*empty*/
                            'CUSDUE_28' => $detail->CUSDUE_28,
                            'PROBAB_28' => 0,
                            'SHPDTE_28' => null,  /*empty*/
                            'FILL04_28' => '', /*empty*/
                            'SLSUOM_28' => 'UN',
                            'REFRNC_28' => $detail->REFRNC_28,
                            'PRICE_28' => $detail->PRICE_28,
                            'ORGQTY_28' => $item->quantity,
                            'CURQTY_28' => $item->quantity,
                            'BCKQTY_28' => 0,
                            'SHPQTY_28' => 0,
                            'DUEQTY_28' => $item->quantity,
                            'INVQTY_28' => 0,
                            'DISC_28' => 0,
                            'STYPE_28' => 'CU',
                            'PRNT_28' => 'N',
                            'AKPRNT_28' => 'N',
                            'STK_28' => $detail->STK_28,
                            'COCFLG_28' => '', /*empty*/
                            'FORCUR_28' => $detail->FORCUR_28,
                            'HSTAT_28' => 'R',
                            'SLSREP_28' => '', /*empty*/
                            'COMMIS_28' => 0,
                            'DRPSHP_28' => '', /*empty*/
                            'QUMQTY_28' => 0,
                            'TAXCDE1_28' => $detail->TAXCDE1_28,
                            'TAX1_28' => ($detail->PRICE_28 * $item->quantity) * 0.19,
                            'TAXCDE2_28' => '', /*empty*/
                            'TAX2_28' => 0,
                            'TAXCDE3_28' => '', /*empty*/
                            'TAX3_28' => 0,
                            'MCOMP_28' => '', /*empty*/
                            'MSITE_28' => '', /*empty*/
                            'UDFKEY_28' => $detail->UDFKEY_28,
                            'UDFREF_28' => '', /*empty*/
                            'DEXPFLG_28' => 'N',
                            'COST_28' => $detail->COST_28,
                            'MARKUP_28' => 0,
                            'QTORD_28' => '', /*empty*/
                            'XDFINT_28' => 0,
                            'XDFFLT_28' => 0,
                            'XDFBOL_28' => '', /*empty*/
                            'XDFDTE_28' => null,
                            'XDFTXT_28' => '', /*empty*/
                            'FILLER_28' => '', /*empty*/
                            'CreatedBy' => 'EVPIU-' . auth()->user()->username,
                            'CreationDate' => Carbon::now(),
                            'ModifiedBy' => 'EVPIU-' . auth()->user()->username,
                            'ModificationDate' => Carbon::now(),
                            'BOKDTE_28' => Carbon::now(),
                            'DBKDTE_28' => null,
                            'REVLEV_28' => '', /*empty*/
                            'MANPRC_28' => 'N',
                            'ORGPRC_28' => $detail->ORGPRC_28,
                            'PRCALC_28' => 2,
                            'CLASS_28' => '', /*empty*/
                            'WARRES_28' => 0,
                            'JOB_28' => '', /*empty*/
                            'CSENDDTE_28' => null,
                            'CONSGND_28' => 0,
                            'CURCONSGND_28' => 0,
                            'CONSIGNSTK_28' => '', /*empty*/
                            'CURSHP_28' => 0,
                        ]);

                    $so_detail_ext = DB::connection('MAX')
                        ->table('SO_Detail_Ext')
                        ->where('ORDER_LIN_DEL', '=', "{$ov}{$detail->LINNUM_28}{$detail->DELNUM_28}")
                        ->first();

                    if ($so_detail_ext) {
                        DB::connection('MAX')
                            ->table('SO_Detail_Ext')
                            ->insert([
                                'ORDER_LIN_DEL' => "{$ov}{$detail->LINNUM_28}{$detail->DELNUM_28}",
                                'ARTE' => $so_detail_ext->ARTE,
                                'CodProdCliente' => $so_detail_ext->CodProdCliente,
                                'Marca' => $so_detail_ext->Marca,
                            ]);
                    }
                }
            }
            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            DB::commit();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function new_invoice(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request_invoice = RequestInvoice::with('items')->find($request->id);

            $invoice = DB::connection('MAX')
                ->table('CIEV_V_FE_FacturasTotalizadas')
                ->where('NUMERO', '=', $request_invoice->invoice)
                ->first();

            $cellar = [
                'items' => [],
                'totals' => [
                    'bruto' => 0,
                    'discount' => 0,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
            ];

            foreach ($request_invoice->items as $item) {
                $item_info = DB::connection('MAX')->
                table('CIEV_V_FE_FacturasDetalladas')
                    ->where('Factura', '=', $request_invoice->invoice)
                    ->where('item', '=', $item->item)->first();

                // values
                $bruto = $item_info->Precio * $item->quantity;
                $discount = 0;
                $subtotal = $bruto - $discount;
                $tax = strval($invoice->CODIVA) === 'IVA-V19' ? ($subtotal * 19) / 100 : 0;
                $total = $subtotal + $tax;

                //totals
                $cellar['totals']['bruto'] += $bruto;
                $cellar['totals']['discount'] += $discount;
                $cellar['totals']['subtotal'] += $subtotal;
                $cellar['totals']['tax'] += $tax;
                $cellar['totals']['total'] += $total;

                $cellar['items'][] = [
                    'product' => $item->product_code,
                    'quantity' => $item->quantity,
                    'price' => $item_info->Precio,
                    'unit_measurement' => $item_info->UM,
                    'art' => $item_info->ARTE,
                    'customer_product_code' => $item_info->CodProdCliente,
                    'type' => 'R',
                    'destiny' => 'C',
                ];
            }

            $order = HeaderOrder::create([
                'customer_code' => $invoice->CLIENTE,
                'notes' => strtoupper($invoice->COMENTARIOS),
                'currency' => $invoice->MONEDA,
                'bruto' => $cellar['totals']['bruto'],
                'discount' => $cellar['totals']['discount'],
                'subtotal' => $cellar['totals']['subtotal'],
                'taxes' => $cellar['totals']['tax'],
                'total' => $cellar['totals']['total'],
                'taxable' => $invoice->CODIVA === 'IVA-V19' ? 1 : 0,
                'created_by' => auth()->user()->id,
                'seller_id' => $invoice->CODVENDEDOR,
                'oc' => $invoice->OC,
                'state' => '4',
                'destiny' => 'C',
                'type' => 'reclamo',
            ]);

            $order->log()->create([
                'description' => 'Nuevo pedido creado',
                'type' => 'user',
                'work_center' => 'sales',
                'created_by' => auth()->user()->id,
            ]);

            $order->details()->createMany($cellar['items']);

            DB::commit();

            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getTrace(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function finished_process(Request $request): JsonResponse
    {
        try {
            RequestInvoice::find($request->id)
                ->update([
                    'process_status' => '4',
                ]);
            $data = RequestInvoice::with('items.detail', 'createdBy', 'reason_reprocessings')
                ->where('process_status', '3')
                ->where('process_status', '5')
                ->get();

            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**_______________________________________________________________________
     * notes = notas ingresadas por el area de calidad
     * comments = comentarios iniciales del vendedor en el reclamo
     * justify = rechazado de calidad
     * justify_send_store = enviado de bodega a calidad
     * justify_refuse_store = rechazado de bodega
     * reopen_quality = reabierto por vendedor y enviado a calidad
     * reopen_store = reabierto vendedor y enviado a bodega
     * _________________________________________________________________________*/

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reopen_store(Request $request): JsonResponse
    {
        RequestInvoice::find($request->id)
            ->update([
                'process_status' => '3',
            ]);

        $data = RequestInvoice::where('process_status', '2')->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reopen_quality(Request $request): JsonResponse
    {
        RequestInvoice::find($request->id)
            ->update([
                'process_status' => '1',
            ]);

        $data = RequestInvoice::where('process_status', '2')->get();

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_comments_vendor(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $req = RequestInvoice::find($request['data']['id']);

            $req->update([
                'comments' => $request['data']['comments'],
                'reopen_quality_comments' => $request['data']['reopen_quality_comments'],
                'reopen_store_comments' => $request['data']['reopen_store_comments'],

            ]);

            foreach ($request['file'] as $file) {
                $file = $file['dataURL'];

                if (preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                    $file = substr($file, strpos($file, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        throw new Exception('invalid image type');
                    }

                    $file = str_replace(' ', '+', $file);
                    $file = base64_decode($file);

                    if ($file === false) {
                        throw new Exception('base64_decode failed');
                    }
                } else {
                    throw new Exception('did not match data URI with image data');
                }

                $filename = uniqid(rand(10000, 99999), false) . ".{$type}";
                $path = 'request/invoices/' . $req->id;
                $fullpath = storage_path() . '/app/request/invoices/' . $req->id . '/' . $filename;

                if (!Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path);
                }

                //Storage::disk('local')->put($path.$filename, $file);
                Storage::disk('local')->put('request/invoices/' . $req->id . '/' . $filename, $file);

                if (file_exists($fullpath)) {
                    $req->update([
                        'file_approved' => $filename,
                    ]);
                } else {
                    DB::rollBack();

                    return response()->json('error saving files ' . $fullpath, 500);
                }
            }

            $data = RequestInvoice::with('items.detail', 'createdBy', 'worksplace', 'reason_reprocessings')
                ->where('process_status', '3')->get();

            DB::commit();

            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getLine(), 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function workplaces(): JsonResponse
    {
        try {
            $name_workplaces = Workplace::all();

            return response()->json($name_workplaces, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send_cash(Request $request)
    {
        RequestInvoice::find($request->id)
            ->update([
                'process_status' => '5',
            ]);

        $data = RequestInvoice::with('items.detail', 'createdBy', 'worksplace', 'reason_reprocessings')
            ->where('process_status', '3')->get();

        return response()->json($data, 200);
    }

    /**
     * @return Response
     */
    public function view_cash(): Response
    {
        $invoice = RequestInvoice::with('items.detail', 'createdBy', 'worksplace', 'reason_reprocessings')->where('process_status', '=', '5')->get();

        return Inertia::render('Applications/ThirdParties/Customers/RequestInvoice/Cash', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @param $id
     * @return string
     *
     * @throws MpdfException
     */
    public function pdf_cash($id)
    {
        $details = RequestInvoice::with('items', 'worksplace', 'reason_reprocessings', 'createdBy')->find($id);

        $pdf = $this->initMPdf();
        $pdf->SetHTMLHeader(View::make('pdfs.request_invoice.header', compact('details')));
        $pdf->SetHTMLFooter(View::make('pdfs.request_invoice.footer', compact('details')));
        $pdf->WriteHTML(View::make('pdfs.request_invoice.template', compact('details')), HTMLParserMode::HTML_BODY);

        return $pdf->Output();
    }

    /**
     * @return Mpdf ;
     *
     * @throws MpdfException
     */
    protected function initMPdf(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $pdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                base_path('public/fonts/roboto/'),
            ]),
            'format' => [215.9, 139.7],
            'fontdata' => $fontData + [
                    'Roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ],
                ],
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 30,
            'margin_bottom' => 2,
            'margin_header' => 2,
            'margin_footer' => 2,
        ]);

        $pdf->WriteHTML(file_get_contents(base_path('resources/views/pdfs/request_invoice/styles.css')), HTMLParserMode::HEADER_CSS);

        return $pdf;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload_files(Request $request)
    {
        try {
            $customer = CustomerMax::where('CODIGO_CLIENTE', '=', $request->customer_code)
                ->first();

            $code = trim($customer->CODIGO_CLIENTE);

            if ($request->file('files')) {
                $files = $request->file('files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                for ($i = 0; $i < count($files); $i++) {
                    $file = $files[$i];

                    $filename = $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);

                    $path = "customers/{$code}/files";

                    $full_path = storage_path() . "/app/customers/{$code}/files/{$filename}";

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $storagePath = Storage::putFileAs("customers/{$code}/files", $file, $filename);

                    if (Storage::exists($storagePath)) {
                        $customer->files()->create([
                            'name' => $filename,
                            'path' => $storagePath,
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json("error saving files: {$full_path}", 500);
                    }
                }
            }

            $files = CustomerFile::where('customer_code', '=', $code)
                ->get();

            return response()->json($files, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_file(Request $request)
    {
        $file = CustomerFile::find($request->id);

        Storage::delete($file->path);

        if (!Storage::exists($file->path)) {
            $file->delete();
            DB::commit();

            $code = trim($request->customer_code);
            $files = CustomerFile::where('customer_code', '=', $code)
                ->get();

            return response()->json($files, 200);
        } else {
            DB::rollBack();

            return response()->json('delete file error', 500);
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function download_file(Request $request): BinaryFileResponse
    {
        $file = CustomerFile::find($request->id);

        return response()->download(storage_path('app/' . $file->path), $file->name);
    }

    /**
     * @param $id
     * @param $filename
     * @return BinaryFileResponse
     */
    public function download($id, $filename): BinaryFileResponse
    {
        // Check if file exists in app/storage/file folder
        $fullpath = storage_path() . '/app/request/invoices/' . $id . '/' . $filename;

        if (file_exists($fullpath)) {
            // Send Download
            return response()->download($fullpath, $filename);
        } else {
            // Error
            exit('Requested file does not exist on our server!');
        }
    }
}
