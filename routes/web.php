<?php

use App\Exports\ExportEmployeeData;
use App\Http\Controllers\ArchaicArtController;
use App\Http\Controllers\ArtsController;
use App\Http\Controllers\Automation\TariffPositionQueryController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BlueprintController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CheckMobilityController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CloseSellOrderController;
use App\Http\Controllers\DesignRequirementController;
use App\Http\Controllers\EditorNotesInvoiceController;
use App\Http\Controllers\ElectronicBilling\DocumentController;
use App\Http\Controllers\ElectronicBilling\Exports\CreditNoteController as ExportsCreditNote;
use App\Http\Controllers\ElectronicBilling\Exports\InvoiceController as ExportsInvoice;
use App\Http\Controllers\ElectronicBilling\National\CreditNoteController as NationalCreditNote;
use App\Http\Controllers\ElectronicBilling\National\DebitNoteController as NationalDebitNote;
use App\Http\Controllers\ElectronicBilling\National\InvoiceController as NationalInvoice;
use App\Http\Controllers\ElectronicBilling\WebServiceController;
use App\Http\Controllers\EmployeeRequestController;
use App\Http\Controllers\Encoder\ClonerController;
use App\Http\Controllers\Encoder\CodeController;
use App\Http\Controllers\Encoder\DescriptionEditionController;
use App\Http\Controllers\Encoder\Masters\DecorativeOptionController;
use App\Http\Controllers\Encoder\Masters\FeatureController;
use App\Http\Controllers\Encoder\Masters\GalvanicFinishController;
use App\Http\Controllers\Encoder\Masters\LineController;
use App\Http\Controllers\Encoder\Masters\MaterialController;
use App\Http\Controllers\Encoder\Masters\MeasurementController;
use App\Http\Controllers\Encoder\Masters\ProductTypeController;
use App\Http\Controllers\Encoder\Masters\SubLineController;
use App\Http\Controllers\Encoder\NewProductController;
use App\Http\Controllers\Encoder\PendingProductController;
use App\Http\Controllers\EnvironmentalManagementController;
use App\Http\Controllers\GalvanoBathParameterController;
use App\Http\Controllers\Goja\EmployeeDocumentController;
use App\Http\Controllers\Goja\HumanResourceController as GojaHumanResourceController;
use App\Http\Controllers\Goja\PayrollController as GojaPayroll;
use App\Http\Controllers\Goja\ReportController as GojaReportController;
use App\Http\Controllers\Goja\SupplierPurchaseController as GojaSupplierPurchase;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\ImportDocumentMaxDmsController;
use App\Http\Controllers\InvoiceEditionController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LdapUserController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Max\InventoryIssueController;
use App\Http\Controllers\Max\LotChangeController;
use App\Http\Controllers\Max\OPOVRelationshipController;
use App\Http\Controllers\Max\PostOperationCompletionController;
use App\Http\Controllers\Max\Reports\Production\PendingController;
use App\Http\Controllers\MaxUpdateController;
use App\Http\Controllers\OpenSellOrderController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Orders\PackingOrderExportationController;
use App\Http\Controllers\Orders\ProductController;
use App\Http\Controllers\Orders\ReportController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PendingController as PendingControllers;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\PlantTransactionController;
use App\Http\Controllers\PointOfSaleRemissionController;
use App\Http\Controllers\PostmarkController;
use App\Http\Controllers\PricePerCustomerController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionSheetController;
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\Queries\AccountReceivableController;
use App\Http\Controllers\Queries\BillingPerDayController as QueriesBillingPerDayController;
use App\Http\Controllers\Queries\CustomerController as QueriesCustomerController;
use App\Http\Controllers\Queries\DeliveryCommitmentController as QueriesDeliveryCommitmentController;
use App\Http\Controllers\Queries\InvoicesPerSellerController as QueriesInvoicesPerSellerController;
use App\Http\Controllers\Queries\OrderShippedNotInvoicedController as QueriesOrderShippedNotInvoicedController;
use App\Http\Controllers\Queries\ProductionOrderStatusController as QueriesProductionOrderStatusController;
use App\Http\Controllers\Queries\ReplaceDataController as QueriesReplaceDataController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReasonValidationController;
use App\Http\Controllers\RemissionController;
use App\Http\Controllers\RemoteAccessController;
use App\Http\Controllers\ReplaceController;
use App\Http\Controllers\Reports\ProductionReportController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SecurityHealthWorkController;
use App\Http\Controllers\SupplierPurchaseController;
use App\Http\Controllers\SupportDocumentController;
use App\Http\Controllers\TemperatureController;
use App\Http\Controllers\ThirdParties\CashRegisterReceipts\AdvanceController;
use App\Http\Controllers\ThirdParties\CashRegisterReceipts\ManagementController as CashRegisterReceiptManagementController;
use App\Http\Controllers\ThirdParties\CashRegisterReceipts\ReceiptController;
use App\Http\Controllers\ThirdParties\CustomerController;
use App\Http\Controllers\ThirdParties\CustomerTransactionController;
use App\Http\Controllers\ThirdParties\ForecastController;
use App\Http\Controllers\UpdateStructureController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group, which
| contains the "web" middleware group. Now create something great!
|
*/

//HOME PAGE
Route::get('/', function () {
    return Inertia::render('Auth/PreLogin');
})->name('pre-login');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    //DASHBOARD
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('editor-notes-invoices', EditorNotesInvoiceController::class)->only('index', 'store');
    Route::get('editor-notes-invoices/get-notes', [EditorNotesInvoiceController::class, 'get_notes'])->name('editor-notes-invoices.get-notes');

    //ARTS LIST
    Route::get('arts', [ArtsController::class, 'index'])->name('arts');
    Route::prefix('arts')->group(function () {
        Route::get('search', [ArtsController::class, 'search']);
    });

    // REMOTE ACCESS
    Route::get('remote_access', [RemoteAccessController::class, 'index'])->name('remote_access');
    Route::prefix('remote_access')->group(function () {
        Route::post('save', [RemoteAccessController::class, 'store']);
        Route::delete('delete/{id}', [RemoteAccessController::class, 'destroy']);
    });

    // FACTURACION ELECTRONICA
    Route::prefix('electronic-billing/{entity}')->group(function () {
        // NACIONALES
        Route::prefix('national')->group(function () {
            // FACTURAS
            Route::prefix('invoices')->group(function () {
                Route::get('/', [NationalInvoice::class, 'index'])->name('electronic_billing.national.invoices');
                Route::get('search-by-date', [NationalInvoice::class, 'search_by_date'])->name('electronic_billing.national.invoices.search-by-date');
                Route::post('send-local-api', [WebServiceController::class, 'send_document_api'])->name('electronic_billing.national.invoices.send-local-api');
                Route::get('remission/{invoice}', [NationalInvoice::class, 'remission'])->name('electronic_billing.national.invoices.remission');
                Route::post('updateOC', [NationalInvoice::class, 'updateOC'])->name('electronic_billing.national.invoices.updateOC');
            });

            // NOTAS CREDITO
            Route::prefix('credit-notes')->group(function () {
                Route::get('/', [NationalCreditNote::class, 'index'])->name('electronic_billing.national.credit_notes');
                Route::get('search-by-date', [NationalCreditNote::class, 'search_by_date'])->name('electronic_billing.national.credit-notes.search-by-date');
                Route::post('send-credit-note', [WebServiceController::class, 'credit_note'])->name('electronic_billing.national.credit-notes.send-credit-note');
            });

            Route::prefix('debit-notes')->group(function () {
                Route::get('/', [NationalDebitNote::class, 'index'])->name('electronic_billing.national.debit_notes');
                Route::get('search-by-date', [NationalDebitNote::class, 'search_by_date'])->name('electronic_billing.national.debit-notes.search-by-date');
                Route::post('send-debit-note', [WebServiceController::class, 'debit_note'])->name('electronic_billing.national.debit-notes.send-debit-note');
            });

            Route::get('/{invoice}/json', [WebServiceController::class, 'invoice_json']);
        });

        // EXPORTS
        Route::prefix('exports')->group(function () {
            // INVOICES
            Route::prefix('invoices')->group(function () {
                Route::get('/', [ExportsInvoice::class, 'index'])->name('electronic_billing.exports.invoices');
                Route::get('search-by-date', [ExportsInvoice::class, 'search_by_date'])->name('electronic_billing.exports.invoices.search-by-date');
                Route::post('send-local-api', [WebServiceController::class, 'invoice_export'])->name('electronic_billing.exports.invoices.send-local-api');
            });

            // CREDIT NOTES
            Route::prefix('credit-notes')->group(function () {
                Route::get('/', [ExportsCreditNote::class, 'index'])->name('electronic_billing.exports.credit-notes');
                Route::get('search-by-date', [ExportsCreditNote::class, 'search_by_date'])->name('electronic_billing.exports.credit-notes.search-by-date');
                Route::post('send-local-api', [WebServiceController::class, 'credit_note_export'])->name('electronic_billing.exports.credit-notes.send-local-api');
            });
        });

        Route::prefix('documents')->group(function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::get('search-customer', [DocumentController::class, 'search_customer'])->name('electronic-billing.documents.search-customer');
            Route::get('get-documents', [DocumentController::class, 'get_documents'])->name('electronic-billing.documents.get-documents');
            Route::get('download/{document}', [DocumentController::class, 'download'])->name('electronic-billing.documents.download');
            Route::post('regenerate', [DocumentController::class, 'regenerate']);

        });
    });

    Route::prefix('encoder')->group(function () {
        Route::resource('codes', CodeController::class)->only('index', 'store', 'update', 'destroy');
        Route::prefix('codes')->group(function () {
            Route::get('verify-product/{code}', [CodeController::class, 'verify_product'])->name('codes.verify-product');
            Route::get('get-lines', [CodeController::class, 'get_lines'])->name('codes.get-lines');
            Route::get('get-sublines', [CodeController::class, 'get_sublines'])->name('codes.get-sublines');
            Route::get('get-other-inputs', [CodeController::class, 'get_other_inputs'])->name('codes.get-other-inputs');
            Route::get('get-list-codes', [CodeController::class, 'get_list_codes'])->name('codes.get-list-codes');
            Route::get('search-art', [CodeController::class, 'search_arts'])->name('codes.search-art');
            Route::get('get-measurements', [CodeController::class, 'get_measurements'])->name('codes.get-measurements');
            Route::get('get-lists', [CodeController::class, 'get_lists'])->name('codes.get-lists');
            Route::post('validate-description', [CodeController::class, 'validate_description'])->name('code.validate-description');
        });

        Route::prefix('pending-product')->group(function () {
            Route::get('', [PendingProductController::class, 'index']);
            Route::post('', [PendingProductController::class, 'post'])->name('pending-product.post');
            Route::post('update-product', [PendingProductController::class, 'update'])->name('pending-product.update');
        });

        Route::get('products', [CodeController::class, 'products'])->name('encoder.products');

        //MASTERS
        Route::prefix('masters')->group(function () {
            //PRODUCT TYPES
            Route::resource('products-types', ProductTypeController::class);
            Route::get('product-types/validate-code/{code}', [ProductTypeController::class, 'validate_code'])->name('product-types.validate-code');
            Route::get('product-types/validate-name/{name}', [ProductTypeController::class, 'validate_name'])->name('product-types.validate-name');

            //LINES
            Route::resource('lines', LineController::class);
            Route::get('lines/validate-code/{code}', [LineController::class, 'validate_code'])->name('lines.validate-code');
            Route::get('lines/validate-name/{name}', [LineController::class, 'validate_name'])->name('lines.validate-name');
            Route::get('lines/validate-abbreviation/{abbreviation}', [LineController::class, 'validate_abbreviation'])->name('lines.validate-abbreviation');

            //SUBLINES
            Route::resource('sublines', SubLineController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('sublines/get-latest-code', [SubLineController::class, 'GetLatestCode'])->name('sublines.get-latest-code');
            Route::get('sublines/validate-name/{line}/{name}', [SubLineController::class, 'validate_name'])->name('sublines.validate-name');
            Route::get('sublines/validate-abbreviation/{line}/{abbreviation}', [SubLineController::class, 'validate_abbreviation'])->name('sublines.validate-abbreviation');

            //FEATURES
            Route::resource('features', FeatureController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('features/get-sublines', [FeatureController::class, 'GetSublines'])->name('features.get-sublines');
            Route::get('features/get-latest-code', [FeatureController::class, 'GetLatestCode'])->name('features.get-latest-code');
            Route::get('features/validate-name/{line}/{subline}/{name}', [FeatureController::class, 'validate_name'])->name('features.validate-name');
            Route::get('features/validate-abbreviation/{line}/{subline}/{abbreviation}', [FeatureController::class, 'validate_abbreviation'])->name('features.validate-abbreviation');

            //FEATURES
            Route::resource('materials', MaterialController::class)->only('index', 'destroy', 'update', 'store');
            Route::get('materials/validate-material', [MaterialController::class, 'validate_material'])->name('materials.validate');

            //MEASUREMENTS
            Route::resource('measurements', MeasurementController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('measurements/get-units-measurements', [MeasurementController::class, 'GetUnitsMeasurements'])->name('measurements.get-units-measurements');
            Route::get('measurements/get-latest-code', [MeasurementController::class, 'GetLatestCode'])->name('measurements.get-latest-code');
            Route::post('measurements/validate-denomination', [MeasurementController::class, 'validate_denomination'])->name('measurements.validate-denomination');

            //GALVANIC FINISHES
            Route::resource('galvanic-finishes', GalvanicFinishController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('galvanic-finishes/get-latest-code', [GalvanicFinishController::class, 'get_latest_code'])->name('galvanic-finishes.get-latest-code');
            Route::get('galvanic-finishes/validate-name/{name}', [GalvanicFinishController::class, 'validate_name'])->name('galvanic-finish.validate-name');
            Route::get('galvanic-finishes/validate-abbreviation/{abbreviation}', [GalvanicFinishController::class, 'validate_abbreviation'])->name('galvanic-finish.validate-abbreviation');

            //DECORATIVE OPTIONS
            Route::resource('decorative-options', DecorativeOptionController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('decorative-options/get-latest-code', [DecorativeOptionController::class, 'get_latest_code'])->name('decorative-options.get-latest-code');
            Route::get('decorative-options/validate-name/{name}', [DecorativeOptionController::class, 'validate_name'])->name('decorative-options.validate-name');
        });

        Route::prefix('description-edition')->group(function () {
            Route::get('', [DescriptionEditionController::class, 'index']);
            Route::post('', [DescriptionEditionController::class, 'store'])->name('description-edition.store');
            Route::get('search-product', [DescriptionEditionController::class, 'search_product'])->name('description-edition.search-product');
            Route::get('get-measurements-features', [DescriptionEditionController::class, 'get_measurements_features'])->name('description-edition.get-measurements-features');
        });
    });

    Route::resource('cloner', ClonerController::class)->only('index', 'store');
    Route::get('cloner/search-products', [ClonerController::class, 'search_product'])->name('cloner.search-product');
    Route::get('cloner/search-product-max', [ClonerController::class, 'search_product_max'])->name('cloner.search-product-max');

    Route::prefix('third-parties')->group(function () {
        Route::resource('cash-register-receipts', ReceiptController::class)->only('index', 'store', 'update', 'destroy', 'create', 'edit');
        Route::prefix('cash-register-receipts')->group(function () {
            Route::get('get-customer-receipts', [ReceiptController::class, 'get_customer_receipts'])->name('cash-register-receipts.get-customer-receipts');
            Route::get('search-document', [ReceiptController::class, 'search_document'])->name('cash-register-receipts.search-document');
            Route::get('search-customer', [ReceiptController::class, 'search_customer'])->name('cash-register-receipts.search-customer');
            Route::get('consult-sales-type', [ReceiptController::class, 'consult_sales_type'])->name('cash-register-receipts.consult-sales-type');
            Route::get('view', [ReceiptController::class, 'view'])->name('cash-register-receipts.view');
            Route::post('cancel', [ReceiptController::class, 'cancel'])->name('cash-register-receipts.cancel');
            Route::post('send-wallet', [ReceiptController::class, 'send_wallet'])->name('cash-register-receipts.send_wallet');
            Route::get('report', [ReceiptController::class, 'report_cash_receipt'])->name('cash-register-receipts.report');
            Route::get('account-status', [ReceiptController::class, 'account_status'])->name('cash-register-receipts.account-status');
            Route::post('account-status-pdf', [ReceiptController::class, 'account_status_pdf'])->name('cash-register-receipts.account-status-pdf');
            Route::get('get-trm', [ReceiptController::class, 'get_trm'])->name('cash-register-receipts.get-trm');
            Route::get('download/{id}', [ReceiptController::class, 'download_cash_receipts_export'])->name('receipt.download_cash_receipts_export');
        });

        Route::resource('advances', AdvanceController::class)->only('index', 'store', 'update', 'destroy', 'create', 'edit');
        Route::get('advances/view', [AdvanceController::class, 'view'])->name('advances.view');
        Route::post('advances/cancel', [AdvanceController::class, 'cancel'])->name('advances.cancel');
        Route::post('advances/send-wallet', [AdvanceController::class, 'send_wallet'])->name('advances.send_wallet');
        Route::get('advances/report', [AdvanceController::class, 'report_advance'])->name('advances.report');

        Route::resource('management-advances-receipt', CashRegisterReceiptManagementController::class)->only('index');
        Route::prefix('management-advances-receipt')->group(function () {
            Route::post('refuse-receipt', [CashRegisterReceiptManagementController::class, 'refuse_receipt'])->name('management-advances-receipt.refuse-receipt');
            Route::post('approve-receipt', [CashRegisterReceiptManagementController::class, 'approve_receipt'])->name('management-advances-receipt.approve-receipt');
            Route::post('refuse-advance', [CashRegisterReceiptManagementController::class, 'refuse_advance'])->name('management-advances-receipt.refuse-advance');
            Route::post('approve-advance', [CashRegisterReceiptManagementController::class, 'approve_advance'])->name('management-advances-receipt.approve-advance');
        });

        Route::resource('forecasts', ForecastController::class)->only('index');
        Route::prefix('forecasts')->group(function () {
            Route::get('get_data', [ForecastController::class, 'get_data'])->name('forecasts.get-data');
            Route::get('get-orders', [ForecastController::class, 'getOrders'])->name('forecasts.get-orders');
            Route::get('get-inventory', [ForecastController::class, 'get_inventory'])->name('forecasts.get-inventory');
            Route::get('get-lots-detail', [ForecastController::class, 'lots_detail'])->name('forecasts.get-lots-detail');
            Route::get('get-committed-amount', [ForecastController::class, 'committed_amount'])->name('forecasts.get-committed-amount');
        });

        Route::resource('customers', CustomerController::class)->only('index', 'show', 'create', 'store');
        Route::get('customers-request-invoice-quality', [CustomerController::class, 'request_invoice_quality'])->name('customers-request-invoice-quality');
        Route::get('customers-request-invoice-store', [CustomerController::class, 'request_invoice_store']);
        Route::get('customers-request-invoice-vendor', [CustomerController::class, 'vendor_request']);
        Route::post('send-cash', [CustomerController::class, 'send_cash'])->name('send_cash');
        Route::get('customers-request-invoice-cash', [CustomerController::class, 'view_cash']);
        Route::get('customers-request-invoice-cash-print-pdf/{id}', [CustomerController::class, 'pdf_cash'])->name('customers-request-invoice-cash-print-pdf');
        Route::get('/download/{id}/{file}', [CustomerController::class, 'download'])->name('customer-request.download');
        Route::get('/download-file/{id}/{file}', [CustomerController::class, 'download_file'])->name('customer-request.download_file');
        Route::get('customers-request-reports_quality', [CustomerController::class, 'reports_quality'])->name('reports_quality');

        Route::prefix('customers')->group(function () {
            Route::post('update-info', [CustomerController::class, 'update_info'])->name('customers.update-info');
            Route::get('invoice-detail/{invoice}', [CustomerController::class, 'invoice_detail'])->name('customers.invoice-detail');
            Route::get('create-foreign-client', [CustomerController::class, 'create_foreign_client'])->name('customers.create-foreign-client');

            Route::post('invoices', [CustomerController::class, 'customer_invoices'])->name('third-parties.customer.invoices');
            Route::post('invoices', [CustomerController::class, 'customer_invoices'])->name('third-parties.customer.invoices');

            Route::post('credit-notes', [CustomerController::class, 'customer_credit_notes'])->name('third-parties.customer.credit-notes');
            Route::post('open-ov', [CustomerController::class, 'customer_open_ov'])->name('third-parties.customer.open-ov');
            Route::post('close-ov', [CustomerController::class, 'customer_close_ov'])->name('third-parties.customer.close-ov');
            Route::get('open-ov/{customer}', [CustomerController::class, 'customer_open_ov'])->name('third-parties.customer.open-ov2');
            Route::post('customer-types', [CustomerController::class, 'customer_types'])->name('third-parties.customer-types');
            Route::post('paid-terms', [CustomerController::class, 'paid_terms'])->name('third-parties.paid-terms');
            Route::post('sellers', [CustomerController::class, 'sellers'])->name('third-parties.sellers');
            Route::post('economic-groups', [CustomerController::class, 'economic_groups'])->name('third-parties.economic-groups');
            Route::post('change-state-customer', [CustomerController::class, 'change_state_customer'])->name('seller-management.customers.change-state-customer');
            Route::post('request-invoice', [CustomerController::class, 'save_request_invoice'])->name('save-request-invoice');
            Route::post('save-request-invoice-quality', [CustomerController::class, 'save_request_invoice_quality'])->name('save-request-invoice-quality');
            Route::post('send-store', [CustomerController::class, 'send_store'])->name('send-store');
            Route::post('save-request-invoice-store', [CustomerController::class, 'save_request_invoice_store'])->name('save-request-invoice-store');
            Route::post('refuse-request-invoice', [CustomerController::class, 'refuse_request_invoice'])->name('refuse-request-invoice');
            Route::post('send-quality', [CustomerController::class, 'send_quality'])->name('send-quality');
            Route::post('refuse-request-invoice-store', [CustomerController::class, 'refuse_request_invoice_store'])->name('refuse-request-invoice-store');
            Route::post('new-order-store', [CustomerController::class, 'new_order'])->name('new_order_store');
            Route::post('new-invoice-store', [CustomerController::class, 'new_invoice'])->name('new_invoice_store');
            Route::post('finished-process', [CustomerController::class, 'finished_process'])->name('finished_process');

            Route::post('reopen-store', [CustomerController::class, 'reopen_store'])->name('reopen_store');
            Route::post('reopen-quality', [CustomerController::class, 'reopen_quality'])->name('reopen_quality');
            Route::post('update-comments-vendor', [CustomerController::class, 'update_comments_vendor'])->name('update-comments-vendor');

            Route::prefix('update-information')->group(function () {
                Route::post('business-name', [CustomerTransactionController::class, 'business_name'])->name('customer.update-information.business-name');
                Route::post('comercial-name', [CustomerTransactionController::class, 'comercial_name'])->name('customer.update-information.comercial-name');
                Route::post('contact', [CustomerTransactionController::class, 'contact'])->name('customer.update-information.contact');
                Route::post('location', [CustomerTransactionController::class, 'location'])->name('customer.update-information.location');
                Route::post('phone-1', [CustomerTransactionController::class, 'phone1'])->name('customer.update-information.phone-1');
                Route::post('phone-2', [CustomerTransactionController::class, 'phone2'])->name('customer.update-information.phone-2');
                Route::post('cellphone', [CustomerTransactionController::class, 'cellphone'])->name('customer.update-information.cellphone');
                Route::post('contact-mail', [CustomerTransactionController::class, 'contact_email'])->name('customer.update-information.contact-mail');
                Route::post('billing-mail', [CustomerTransactionController::class, 'billing_email'])->name('customer.update-information.billing-mail');
                Route::post('copy-mails', [CustomerTransactionController::class, 'copy_mails'])->name('customer.update-information.copy-mails');
                Route::post('address-1', [CustomerTransactionController::class, 'address1'])->name('customer.update-information.address-1');
                Route::post('address-2', [CustomerTransactionController::class, 'address2'])->name('customer.update-information.address-2');
                Route::post('currency', [CustomerTransactionController::class, 'currency'])->name('customer.update-information.currency');
                Route::post('customer-type', [CustomerTransactionController::class, 'customer_type'])->name('customer.update-information.customer-type');
                Route::post('gravado', [CustomerTransactionController::class, 'gravado'])->name('customer.update-information.gravado');
                Route::post('payment-term', [CustomerTransactionController::class, 'payment_term'])->name('customer.update-information.payment-term');
                Route::post('discount-rate', [CustomerTransactionController::class, 'discount_rate'])->name('customer.update-information.discount-rate');
                Route::post('seller', [CustomerTransactionController::class, 'seller'])->name('customer.update-information.seller');
                Route::post('electronic-invoicing-manager', [CustomerTransactionController::class, 'electronic_invoicing_manager'])->name('customer.update-information.electronic-invoicing-manager');
                Route::post('electronic-invoicing-phone', [CustomerTransactionController::class, 'electronic_invoicing_phone'])->name('customer.update-information.electronic-invoicing-phone');
                Route::post('foreign-city-code', [CustomerTransactionController::class, 'foreign_city_code'])->name('customer.update-information.foreign-city-code');
                Route::post('rut-delivered', [CustomerTransactionController::class, 'rut_delivered'])->name('customer.update-information.rut-delivered');
                Route::post('great-contributor', [CustomerTransactionController::class, 'great_contributor'])->name('customer.update-information.great-contributor');
                Route::post('responsible-taxes', [CustomerTransactionController::class, 'responsible_taxes'])->name('customer.update-information.responsible-taxes');
                Route::post('economic-activity', [CustomerTransactionController::class, 'economic_activity'])->name('customer.update-information.economic-activity');
            });

            Route::prefix('files')->group(function () {
                Route::post('upload', [CustomerController::class, 'upload_files'])->name('customer.files.upload');
                Route::post('download', [CustomerController::class, 'download_file'])->name('customer.files.download');
                Route::post('delete', [CustomerController::class, 'delete_file'])->name('customer.files.delete');
            });

            Route::prefix('global-data')->group(function () {
                Route::get('countries', [CustomerTransactionController::class, 'countries'])->name('customer-transaction.global-data.countries');
                Route::get('departments/{country_code}', [CustomerTransactionController::class, 'departments'])->name('customer-transaction.global-data.departments');
                Route::get('cities/{country_code}/{department_code}', [CustomerTransactionController::class, 'cities'])->name('customer-transaction.global-data.cities');
            });
        });
        Route::get('wizard', [CustomerController::class, 'wizard'])->name('customers.wizard');

        Route::get('customers-credit-limit-gestion', [CustomerController::class, 'credit_limit_gestion'])->name('customers.credit-limit-gestion');
        Route::prefix('customers-credit-limit-gestion')->group(function () {
            Route::get('view', [CustomerController::class, 'credit_limit_gestion_show'])->name('customers.credit-limit-gestion.view');
            Route::post('store-new-customer', [CustomerController::class, 'store_new_customer'])->name('customers.credit-limit-gestion.store-new-customer');
        });

        Route::get('inventory', [CustomerController::class, 'inventory'])->name('inventory');
        Route::get('query-inventory', [CustomerController::class, 'query_inventory'])->name('query-inventory');
        Route::get('available_amount/{reference}', [CustomerController::class, 'available_amount'])->name('inventory.available_amount');
        Route::post('inventory/enable-product', [CustomerController::class, 'enable_product'])->name('inventory.enable-product');
    });

    Route::prefix('environmental-management')->group(function () {
        Route::get('chimney-gas', [EnvironmentalManagementController::class, 'index']);
        Route::prefix('chimney-gas')->group(function () {
            Route::get('chimney-new-data', [EnvironmentalManagementController::class, 'chimney_new_data'])->name('chimney-gas.chimney-new-data');
        });

        Route::prefix('machines')->group(function () {
            Route::get('/', [EnvironmentalManagementController::class, 'machines']);
            Route::post('store', [EnvironmentalManagementController::class, 'store_machine'])->name('env-management.machines.store');
            Route::put('update/{id}', [EnvironmentalManagementController::class, 'update_machine'])->name('env-management.machines.update');
            Route::post('delete/{id}', [EnvironmentalManagementController::class, 'delete_machine'])->name('env-management.machines.delete');
        });

        Route::prefix('binnacle-omff')->group(function () {
            Route::get('management', [EnvironmentalManagementController::class, 'management']);
            Route::prefix('registry')->group(function () {
                Route::get('p0xx', [EnvironmentalManagementController::class, 'p0xx']);
                Route::post('p0xx/store', [EnvironmentalManagementController::class, 'p0xx_store'])->name('binnacle-omff.registry.p0xx.store');
                Route::get('p0xx/details', [EnvironmentalManagementController::class, 'p0xx_details'])->name('binnacle-omff.registry.p0xx.details');

                Route::get('hl1', [EnvironmentalManagementController::class, 'hl1']);
                Route::post('hl1/store', [EnvironmentalManagementController::class, 'hl1_store'])->name('binnacle-omff.registry.hl1.store');
                Route::get('hl1/details', [EnvironmentalManagementController::class, 'hl1_details'])->name('binnacle-omff.registry.hl1.details');
            });
        });

        Route::post('disable-notify', [EnvironmentalManagementController::class, 'disable_notify'])->name('environmental-management.disable-notify');
    });

    Route::resource('design-requirements', DesignRequirementController::class)->only('create', 'store', 'index', 'destroy');

    Route::prefix('design-requirements')->group(function () {
        Route::get('verify/{id}', [DesignRequirementController::class, 'verify_requirement'])->name('design-requirements.verify');
        Route::post('finalize', [DesignRequirementController::class, 'finalize'])->name('design-requirement.finalize');
        Route::post('change-product', [DesignRequirementController::class, 'change_product'])->name('design-requirement.change-product');

        Route::get('arts', [ArtsController::class, 'design_requirement_arts']);
        Route::get('arts/{param}/{column?}', [ArtsController::class, 'print'])->name('arts.print');
        Route::get('arts/check-versions/{id}', [ArtsController::class, 'check_versions'])->name('arts.check-versions');

        Route::get('get-brands', [DesignRequirementController::class, 'get_brands'])->name('design-requirements.get-brands');
        Route::get('get-measurements', [DesignRequirementController::class, 'get_measurements'])->name('design-requirements.get-measurements');
        Route::get('verify-brand/{name}', [DesignRequirementController::class, 'verify_brand'])->name('design-requirements.verify-brand');
        Route::get('requirement/{id}', [DesignRequirementController::class, 'design_requirement'])->name('design-requirements.requirement');
        Route::get('search-product', [DesignRequirementController::class, 'search_product'])->name('design-requirements.search-product');

        Route::post('upload-file', [DesignRequirementController::class, 'upload_file'])->name('design-requirements.upload-file');
        Route::post('download-file', [DesignRequirementController::class, 'download_file'])->name('design-requirements.download-file');
        Route::post('remove-file', [DesignRequirementController::class, 'remove_file'])->name('design-requirements.remove-file');
        Route::post('add-comment', [DesignRequirementController::class, 'add_comment'])->name('design-requirements.add-comment');
        Route::post('change-brand', [DesignRequirementController::class, 'change_brand'])->name('design-requirements.change-brand');
        Route::post('change-designer', [DesignRequirementController::class, 'change_designer'])->name('design-requirements.change-designer');
        Route::post('change-state', [DesignRequirementController::class, 'change_state'])->name('design-requirements.change-state');
        Route::post('change-measurement', [DesignRequirementController::class, 'change_measurement'])->name('design-requirements.change-measurement');

        Route::post('proposal-store', [DesignRequirementController::class, 'proposal_store'])->name('design-requirements.proposal-store');
        Route::post('clone-proposal', [DesignRequirementController::class, 'clone_proposal'])->name('design-requirements.clone-proposal');

        Route::get('proposal-print/{id}', [DesignRequirementController::class, 'proposal_print'])->name('design-requirements.proposal-print');
        Route::get('old-version-proposal-print/{id}', [DesignRequirementController::class, 'old_proposal_print'])->name('design-requirements.old-version-proposal-print');
        Route::post('proposal/add-comment', [DesignRequirementController::class, 'proposal_add_comment'])->name('design-requirements.proposal.add-comment');
        Route::post('proposal/update', [DesignRequirementController::class, 'proposal_update'])->name('design-requirements.proposal.update');
        Route::post('proposal/gestion-3d/return-without-gestion', [DesignRequirementController::class, 'return_without_gestion'])->name('design-requirement.return-without-gestion');


        Route::get('requirement-info/{id}', [DesignRequirementController::class, 'requirement_info'])->name('design-requirements.requirement-info');
        Route::get('product-info/{code}', [DesignRequirementController::class, 'product_info'])->name('design-requirement.product-info');

        // search
        Route::get('/search-brands/{q}', [DesignRequirementController::class, 'search_brands'])->name('design-requirement.search-brands');
        Route::get('/search-products/{q}', [DesignRequirementController::class, 'search_products'])->name('design-requirement.search-products');
        Route::get('/search-measurements/{q}', [DesignRequirementController::class, 'search_measurements'])->name('design-requirement.search-measurements');

        Route::post('send-design', [DesignRequirementController::class, 'send_design'])->name('design-requirements.send-design');
        Route::post('cancel', [DesignRequirementController::class, 'cancel'])->name('design-requirements.cancel');
        Route::post('assign-designer', [DesignRequirementController::class, 'assign_designer'])->name('design-requirements.assign-designer');
        Route::post('refuse', [DesignRequirementController::class, 'refuse'])->name('design-requirements.refuse');

        Route::get('proposals/gestion-3d', [DesignRequirementController::class, 'gestion_3d']);
        Route::post('proposals/gestion-3d/update', [DesignRequirementController::class, 'update_3D'])->name('design-requirements.proposal.update-3D');
        Route::post('proposals/gestion-3d/update-proposal-3d', [DesignRequirementController::class, 'update_proposal_3d'])->name('design-requirements.proposal.update-proposal-3d');
        Route::post('proposal/change-state', [DesignRequirementController::class, 'proposal_change_state'])->name('design-requirements.proposal.change-state');
        Route::get('proposals/validate-blueprint/{id}', [DesignRequirementController::class, 'proposal_validate_blueprint'])->name('design-requirements.proposal.validate-blueprint');

        Route::prefix('blueprint-management')->group(function () {
            Route::get('/', [DesignRequirementController::class, 'gestion_blueprint']);
            Route::post('update', [DesignRequirementController::class, 'update_blueprint'])->name('design-requirements.blueprint-management.update');
            Route::get('load', [DesignRequirementController::class, 'load_blueprints'])->name('design-requirements.blueprint-management.load');
            Route::get('identify/{proposal_id}', [DesignRequirementController::class, 'identify_blueprint'])->name('design-requirements.blueprint-management.identify');
        });

        Route::prefix('new-product')->group(function () {
            Route::get('fields', [NewProductController::class, 'fields'])->name('design-requirements.new-product.fields');
            Route::get('product', [NewProductController::class, 'product'])->name('design-requirements.new-product.product');
            Route::get('get-measurement-by-art', [NewProductController::class, 'get_measurement_by_art'])->name('design-requirements.new-product.get-measurement-by-art');
            Route::get('search-art/{art}', [NewProductController::class, 'search_art'])->name('design-requirements.new-product.search-art');
            Route::post('store-product', [NewProductController::class, 'store_product'])->name('design-requirements.new-product.store-product');
            Route::post('store-product-v2', [NewProductController::class, 'store_product_v2'])->name('design-requirements.new-product.store-product-v2');
        });
    });

    Route::resource('blueprints', BlueprintController::class);

    Route::resource('brands', BrandController::class)->only('store');

    Route::put('change-password', [LdapUserController::class, 'ChangePassword'])->name('change-password.update');

    /*roles y permisos*/
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
    Route::resource('users', UserController::class);

    Route::get('backup/download/{file_name}', [BackupController::class, 'download'])->name('backup.download');
    Route::get('backup/delete/{file_name}', [BackupController::class, 'delete'])->name('backup.delete');
    Route::resource('backup', BackupController::class)->only('index', 'create', 'store');

    Route::resource('orders', OrderController::class)->only('create', 'index', 'store', 'edit', 'update');
    Route::prefix('orders')->group(function () {
        Route::get('search-customer', [OrderController::class, 'search_customer']);
        Route::get('search-customer-seller', [OrderController::class, 'search_customer_seller'])->name('order.search_customer_seller');
        Route::get('suggested-products/{code}', [OrderController::class, 'suggested_products'])->name('orders.suggested-products');

        Route::get('search-arts', [OrderController::class, 'search_arts']);
        Route::get('search-brands', [OrderController::class, 'search_brands']);
        Route::get('search-products', [OrderController::class, 'search_products'])->name('order.search-products');
        Route::get('wallet', [OrderController::class, 'wallet'])->name('orders.wallet');
        Route::get('costs', [OrderController::class, 'costs'])->name('orders.costs');
        Route::get('production', [OrderController::class, 'production'])->name('orders.production');
        Route::get('cellar', [OrderController::class, 'cellar'])->name('orders.cellar');
        Route::get('dies', [OrderController::class, 'dies'])->name('orders.dies');
        Route::post('send-wallet', [OrderController::class, 'send_wallet'])->name('orders.send_wallet');
        Route::post('cancel', [OrderController::class, 'cancel_order'])->name('orders.cancel');
        Route::post('reopen', [OrderController::class, 'reopen_order'])->name('orders.reopen');
        Route::get('view', [OrderController::class, 'view'])->name('orders.view');
        Route::get('log-data', [OrderController::class, 'log_data'])->name('orders.log_data');
        Route::get('customer-product-prices', [OrderController::class, 'customer_product_prices'])->name('order.customer-product-prices');
        Route::put('customer-product-prices/update/{id}', [OrderController::class, 'customer_product_prices_update'])->name('order.customer-product-prices.update');
        Route::get('customer-product-prices-get-info', [OrderController::class, 'customer_product_prices_get_info'])->name('order.customer-product-prices.get-info');
        Route::get('verify-sold-art/{product}/{art}', [OrderController::class, 'verify_sold_art'])->name('order.verify-sold-art');

        Route::post('clone', [OrderController::class, 'clone_order'])->name('orders.clone');
        Route::get('validate-oc/{oc}/{customer}', [OrderController::class, 'validate_oc'])->name('orders.validate-oc');

        Route::prefix('wallet')->group(function () {
            Route::post('approve', [OrderController::class, 'wallet_approve'])->name('orders.wallet.approve');
            Route::post('refuse', [OrderController::class, 'wallet_refuse'])->name('orders.wallet.refuse');
        });

        Route::prefix('costs')->group(function () {
            Route::post('approve', [OrderController::class, 'costs_approve'])->name('orders.costs.approve');
            Route::post('refuse', [OrderController::class, 'costs_refuse'])->name('orders.costs.refuse');
        });

        Route::post('cellar/refuse', [OrderController::class, 'cellar_refuse'])->name('orders.cellar.refuse');
        Route::post('production/refuse', [OrderController::class, 'production_refuse'])->name('orders.production.refuse');
        Route::post('dies/refuse', [OrderController::class, 'dies_refuse'])->name('orders.dies.refuse');
        Route::post('production/send-wallet', [OrderController::class, 'production_send_wallet'])->name('orders.production.send-wallet');

        Route::post('finalize-order', [OrderController::class, 'finalize_order'])->name('orders.finalize_order');
        Route::post('mark-order', [OrderController::class, 'mark_order'])->name('orders.mark-order');

        Route::get('files/{order_master}/{filename}', function ($order_master = null, $file_name = null) {
            $path = storage_path('app/orders/master/' . $order_master . '/' . $file_name);
            if (file_exists($path)) {
                return response()->download($path);
            }
        });

        Route::get('view-pdf/{order}', [OrderController::class, 'view_pdf'])->name('orders.view-pdf');

        Route::get('product-customer', [OrderController::class, 'product_customer'])->name('order.product_customer');

        Route::prefix('reports')->group(function () {
            Route::controller(ReportController::class)->group(function () {
                Route::get('', 'index');
            });
        });
    });

    Route::prefix('packing-order-exportation')->group(function () {
        Route::get('', [PackingOrderExportationController::class, 'index'])->name('packing-order-exportation.index');
        Route::post('', [PackingOrderExportationController::class, 'store'])->name('packing-order-exportation.store');
        Route::put('{id}', [PackingOrderExportationController::class, 'update'])->name('packing-order-exportation.update');
        Route::get('list', [PackingOrderExportationController::class, 'list'])->name('packing-order-exportation.list');
        Route::get('pack-list', [PackingOrderExportationController::class, 'pack_list'])->name('packing-order-exportation.pack-list');
        Route::post('labels-pdf', [PackingOrderExportationController::class, 'labels_pdf'])->name('packing-order-exportation.labels-pdf');
        Route::post('packing-pdf', [PackingOrderExportationController::class, 'packing_pdf'])->name('packing-order-exportation.packing-pdf');
        Route::post('change-state', [PackingOrderExportationController::class, 'change_state'])->name('packing-order-exportation.change-state');
        Route::get('show-op', [PackingOrderExportationController::class, 'show_op'])->name('packing-order-exportation.show-op');
    });

    Route::resource('maintenance', MaintenanceController::class)->only('create', 'store');
    Route::prefix('maintenance')->group(function () {
        Route::post('cancel', [MaintenanceController::class, 'cancel'])->name('maintenance.cancel');
        Route::get('my-requests', [MaintenanceController::class, 'my_requests'])->name('maintenance.my-requests');
        Route::get('my-requests/{id}', [MaintenanceController::class, 'view'])->name('maintenance.my-request.view');
        Route::post('add-comment', [MaintenanceController::class, 'add_comment'])->name('maintenance.add-comment');
        Route::post('store-work-order', [MaintenanceController::class, 'store_work_order'])->name('maintenance.store-work-order');
        Route::post('update-work-order', [MaintenanceController::class, 'update_work_order'])->name('maintenance.update-work-order');
        Route::get('work-order/{id}', [MaintenanceController::class, 'view_work_order'])->name('maintenance.view-work-order');
        Route::post('store-activity', [MaintenanceController::class, 'store_activity'])->name('maintenance.store-activity');
        Route::post('store-conclusion-activity', [MaintenanceController::class, 'store_conclusion_activity'])->name('maintenance.store-conclusion-activity');
        Route::post('finalize-work-order/{id}', [MaintenanceController::class, 'finalize_work_order'])->name('maintenance.store-finalize-work-order');
        Route::post('finalize-maintenance/{id}', [MaintenanceController::class, 'finalize_maintenance'])->name('maintenance.finalize');
        Route::post('update-state', [MaintenanceController::class, 'update_state'])->name('maintenance.update-state');
        Route::post('refuse', [MaintenanceController::class, 'refuse'])->name('maintenance.refuse');
        Route::post('cancel-work-order', [MaintenanceController::class, 'cancel_work_order'])->name('maintenance.cancel-work-order');
        Route::post('cancel-activity', [MaintenanceController::class, 'cancel_activity'])->name('maintenance.cancel-activity');

        Route::prefix('assets')->group(function () {
            Route::get('', [MaintenanceController::class, 'assets'])->name('maintenance.assets.index');
            Route::post('', [MaintenanceController::class, 'assets_store'])->name('maintenance.assets.store');
            Route::delete('', [MaintenanceController::class, 'assets_destroy'])->name('maintenance.assets.destroy');
            Route::post('/{id}', [MaintenanceController::class, 'assets_update'])->name('maintenance.assets.update');
            Route::get('validate-code/{value}', [MaintenanceController::class, 'assets_validate_code'])->name('maintenance.assets.validate-code');
            Route::get('validate-name/{value}', [MaintenanceController::class, 'assets_validate_name'])->name('maintenance.assets.validate-name');
            Route::get('/reports/{id}', [MaintenanceController::class, 'asset_report'])->name('maintenance.asset.report');

            Route::prefix('files')->group(function () {
                Route::post('delete', [MaintenanceController::class, 'delete_file'])->name('maintenance.assets.files.delete');
                Route::post('download', [MaintenanceController::class, 'download_file'])->name('maintenance.assets.files.download');
            });

            Route::post('resume/update', [MaintenanceController::class, 'resume_update'])->name('maintenance.asset.resume-update');
            Route::get('resume/pdf/{id}', [MaintenanceController::class, 'resume_pdf'])->name('maintenance.asset.resume-pdf');
        });

        Route::prefix('asset-classifications')->group(function () {
            Route::get('', [MaintenanceController::class, 'asset_classifications'])->name('maintenance.asset-classification.index');
            Route::post('', [MaintenanceController::class, 'asset_classification_store'])->name('maintenance.asset-classification.store');
            Route::delete('/{id}', [MaintenanceController::class, 'asset_classifications_destroy'])->name('maintenance.asset-classification.destroy');
            Route::put('/{id}', [MaintenanceController::class, 'asset_classifications_update'])->name('maintenance.asset-classification.update');
            Route::get('validate-name/{value}', [MaintenanceController::class, 'asset_classifications_validate_name'])->name('maintenance.asset-classification.validate-name');
        });

        Route::prefix('work-centers')->group(function () {
            Route::get('', [MaintenanceController::class, 'work_centers'])->name('maintenance.work-center.index');
            Route::post('', [MaintenanceController::class, 'work_center_store'])->name('maintenance.work-center.store');
            Route::delete('/{id}', [MaintenanceController::class, 'work_center_destroy'])->name('maintenance.work-center.destroy');
            Route::put('/{id}', [MaintenanceController::class, 'work_center_update'])->name('maintenance.work-center.update');
            Route::get('validate-name/{value}', [MaintenanceController::class, 'work_center_validate_name'])->name('maintenance.work-center.validate-name');
        });

        Route::prefix('report')->group(function () {
            Route::get('', [MaintenanceController::class, 'report'])->name('maintenance.report');
            Route::get('download', [MaintenanceController::class, 'download_report'])->name('maintenance.download-report');
        });

        Route::get('work-orders', [MaintenanceController::class, 'work_order'])->name('maintenance.work-orders');

        Route::prefix('schedule')->group(function (){
            Route::get('preventive', [MaintenanceController::class, 'maintenance_schedule_preventive'])->name('maintenance_schedule_preventive');
            Route::post('search', [MaintenanceController::class, 'maintenance_schedule_search'])->name('maintenance_schedule_search');
            Route::get('pdf-preventive/{date_range}', [MaintenanceController::class, 'pdf_preventive'])->name('maintenance.pdf_preventive');

        });
     });

    Route::prefix('human-resource')->group(function () {
        Route::get('proof-payment', [HumanResourceController::class, 'index']);
        Route::get('search-employee', [HumanResourceController::class, 'search_employee']);
        Route::get('get-months', [HumanResourceController::class, 'get_months'])->name('human-resource.proof-payment.get-months');
        Route::get('get-periods', [HumanResourceController::class, 'get_periods'])->name('human-resource.proof-payment.get-periods');
        Route::post('send-mail', [HumanResourceController::class, 'send_mail'])->name('human-resource.proof-payment.send-mail');
        Route::post('download', [HumanResourceController::class, 'download'])->name('human-resource.proof-payment.download');

        Route::get('gestion-working-letter', [EmployeeRequestController::class, 'gestion_working_letter']);
        Route::post('refuse-working-letter', [EmployeeRequestController::class, 'refuse_working_letter'])->name('refuse-working-letter');
        Route::post('approve-working-letter', [EmployeeRequestController::class, 'approve_working_letter'])->name('approve-working-letter');
        Route::put('edit-working-letter', [EmployeeRequestController::class, 'edit_working_letter'])->name('edit-working-letter');
        Route::post('generate-working-letter', [EmployeeRequestController::class, 'generate_working_letter'])->name('generate-working-letter');

        Route::get('peace-safe', [HumanResourceController::class, 'peace_safe']);
        Route::get('search-retired-employee', [HumanResourceController::class, 'search_retired_employee'])->name('search-retired-employee');
        Route::post('peace-safe/generate-document', [HumanResourceController::class, 'peace_safe_generate_document'])->name('peace-safe.generate-document');
    });

    Route::resource('temperature-control', TemperatureController::class);
    Route::post('temperature-control/save', [TemperatureController::class, 'create_temperature'])->name('temperature-control.save');

    Route::prefix('pending')->group(function () {
        Route::get('', [PendingControllers::class, 'dataop']);
        Route::get('ov', [PendingControllers::class, 'ovopen']);
        Route::get('date', [PendingControllers::class, 'dataov']);
        Route::get('samples', [PendingControllers::class, 'samples']);
        Route::get('hot', [PendingControllers::class, 'hot']);
        Route::get('summary', [PendingControllers::class, 'summary']);

    });


    Route::prefix('price-per-customer')->group(function () {
        Route::get('', [PricePerCustomerController::class, 'index'])->name('price-per-customer.index');
        Route::get('get-data', [PricePerCustomerController::class, 'get_data'])->name('price-per-customer.get-data');
        Route::post('download', [PricePerCustomerController::class, 'download'])->name('price-per-customer.download');
    });

    Route::prefix('goja')->group(function () {
        Route::prefix('human-resource')->group(function () {
            Route::get('proof-payment', [GojaHumanResourceController::class, 'index']);
            Route::get('search-employee', [GojaHumanResourceController::class, 'search_employee']);
            Route::get('get-months', [GojaHumanResourceController::class, 'get_months'])->name('goja.human-resource.proof-payment.get-months');
            Route::get('get-periods', [GojaHumanResourceController::class, 'get_periods'])->name('goja.human-resource.proof-payment.get-periods');
            Route::post('send-mail', [GojaHumanResourceController::class, 'send_mail'])->name('goja.human-resource.proof-payment.send-mail');
            Route::post('download', [GojaHumanResourceController::class, 'download'])->name('goja.human-resource.proof-payment.download');
        });

        Route::prefix('payroll')->group(function () {
            Route::get('/', [GojaPayroll::class, 'index']);
            Route::get('employees', [GojaPayroll::class, 'get_employees'])->name('goja.payroll.get-employees');
            Route::post('send-api', [GojaPayroll::class, 'sendPayroll'])->name('goja.payroll.send-api');
        });

        Route::prefix('supplier-purchases')->group(function () {
            Route::get('', [GojaSupplierPurchase::class, 'index'])->name('goja.supplier-purchases.index');
            Route::post('import', [GojaSupplierPurchase::class, 'import'])->name('goja.supplier-purchases.import');
            Route::post('download-file', [GojaSupplierPurchase::class, 'download_file'])->name('goja.supplier-purchases.download-file');
            Route::post('change-state', [GojaSupplierPurchase::class, 'change_state'])->name('goja.supplier-purchases.change-state');
            Route::post('notify-reception', [GojaSupplierPurchase::class, 'notify_reception'])->name('goja.supplier-purchases.notify-reception');

        });

        Route::prefix('employee-documents')->group(function () {
            Route::get('', [EmployeeDocumentController::class, 'index']);
            Route::post('generate-document', [EmployeeDocumentController::class, 'generate_document'])->name('employee-documents.generate_document');
        });

        Route::prefix('reports')->group(function () {
            Route::prefix('sales-per-product')->group(function () {
                Route::get('', [GojaReportController::class, 'sales_per_product'])->name('goja.reports.sales-per-product.index');
                Route::get('search', [GojaReportController::class, 'sales_per_product_search'])->name('goja.reports.sales-per-product.search');
                Route::get('download', [GojaReportController::class, 'sales_per_product_download'])->name('goja.reports.sales-per-product.download');
            });
        });
    });

    Route::get('validate-client/{document}', [CustomerController::class, 'validate_client'])->name('validate-client');

    Route::get('business-name/{business_name}', [CustomerController::class, 'business_name'])->name('business-name');

    Route::get('get-provinces', [CustomerController::class, 'GetProvinces'])->name('get-provinces');
    Route::get('get-cities', [CustomerController::class, 'GetCities'])->name('get-cities');

    Route::prefix('queries')->group(function () {
        Route::get('customers', [QueriesCustomerController::class, 'index']);
        Route::post('active-customer', [QueriesCustomerController::class, 'active_customer'])->name('queries.customers.active-customer');
        Route::post('inactive-customer', [QueriesCustomerController::class, 'inactive_customer'])->name('queries.customers.inactive-customer');

        Route::get('billing-per-day', [QueriesBillingPerDayController::class, 'index']);
        Route::get('billing-per-day/invoices', [QueriesBillingPerDayController::class, 'invoices'])->name('queries.billing-per-day.invoices');

        Route::get('replace-data', [QueriesReplaceDataController::class, 'index'])->name('queries.replace-data');
        Route::get('replace-data-get-tables', [QueriesReplaceDataController::class, 'get_tables'])->name('queries.replace-data.get-tables');
        Route::get('replace-data-get-columns', [QueriesReplaceDataController::class, 'get_columns'])->name('queries.replace-data.get-columns');
        Route::get('replace-data-count-data', [QueriesReplaceDataController::class, 'count_data'])->name('queries.replace-data.count-data');
        Route::post('replace-data-replace', [QueriesReplaceDataController::class, 'replace'])->name('queries.replace-data.replace');
        Route::get('customer-product-oc', [QueryController::class, 'index']);
        Route::get('customer-product-oc/search', [QueryController::class, 'search'])->name('customer-product-oc.search');

        Route::get('{entity}/open-sell-orders', [OpenSellOrderController::class, 'index']);
        Route::get('{entity}/open-sell-orders/ov/{ov}', [OpenSellOrderController::class, 'show_ov'])->name('open-sell-order.show-ov');

        Route::get('delivery-commitment', [QueriesDeliveryCommitmentController::class, 'index'])->name('queries.delivery-commitment');
        Route::get('production-order-status', [QueriesProductionOrderStatusController::class, 'index'])->name('queries.production-order-status');

        Route::get('order-shipped-not-invoiced', [QueriesOrderShippedNotInvoicedController::class, 'index'])->name('queries.order-shipped-not-invoiced.index');
        Route::get('order-shipped-not-invoiced/{order_id}/show', [QueriesOrderShippedNotInvoicedController::class, 'show'])->name('queries.order-shipped-not-invoiced.show');
        Route::post('order-shipped-not-invoiced/close-order', [QueriesOrderShippedNotInvoicedController::class, 'close_order'])->name('queries.order-shipped-not-invoiced.close-order');

        Route::get('invoices-per-seller', [QueriesInvoicesPerSellerController::class, 'index'])->name('queries.invoices-per-seller.index');
        Route::get('invoices-per-seller/search', [QueriesInvoicesPerSellerController::class, 'search'])->name('queries.invoices-per-seller.search');
        Route::get('invoices-per-seller/show-invoice/{invoice}', [QueriesInvoicesPerSellerController::class, 'show'])->name('queries.invoices-per-seller.show-invoice');
        Route::get('codes-list', [QueryController::class, 'codes_list'])->name('query.codes-list');

        Route::prefix('close-sell-order')->group(function () {
            Route::get('', [CloseSellOrderController::class, 'index']);
            Route::get('search', [CloseSellOrderController::class, 'search'])->name('queries.close-sell-order.search');
        });
    });

    Route::post('refuse-vacation-human-resource', [EmployeeRequestController::class, 'refuse_vacation_human_resource'])->name('refuse-vacation-human-resource');
    Route::post('approve-vacation-human-resource', [EmployeeRequestController::class, 'approve_vacation_human_resource'])->name('approve-vacation-human-resource');
    Route::put('edit-vacation-human-resource', [EmployeeRequestController::class, 'edit_working_vacation_human_resource'])->name('edit-vacation-human-resource');

    Route::resource('raw-material', RawMaterialController::class)->only('index', 'store', 'create');
    Route::get('raw/search-order', [RawMaterialController::class, 'search_order']);
    Route::get('raw/detail-order', [RawMaterialController::class, 'detail_order'])->name('raw-material.detail-order');

    Route::get('online-users', [UserController::class, 'online_users'])->name('online-users');
    Route::post('store-user',[UserController::class, 'store'])->name('store-users');

    Route::get('permissions-list', [PermissionController::class, 'permissions_list']);
    Route::get('roles-list', [RoleController::class, 'roles_list']);

    Route::get('vacation-request-human-resource', [EmployeeRequestController::class, 'vacation_request_human_resource']);
    Route::get('vacation-request-print-pdf/{id}', [EmployeeRequestController::class, 'print_pdf'])->name('vacation-request-print-pdf');

    Route::post('send-mail-invoice-dian/{entity}', [DocumentController::class, 'send_mail_invoice_dian'])->name('send-mail-invoice-dian');
    Route::get('download-invoice-dian/{company}/{prefix}/{document}/{entity}', [DocumentController::class, 'download_invoice_dian'])->name('download-invoice-dian');

    Route::prefix('payroll')->group(function () {
        Route::get('/', [PayrollController::class, 'index']);
        Route::get('employees', [PayrollController::class, 'get_employees'])->name('payroll.get-employees');

        Route::post('send-api', [PayrollController::class, 'sendPayroll'])->name('payroll.send-api');
    });

    Route::get('pending-wallet', function () {
        $data = DB::connection('DMS')
            ->table('v_cartera_edades_CIEV_v1')
            ->where('saldo', '>', 0)
            ->get()->groupBy('nit');

        $data->map(function ($row) {
            return $row;
        });

        return response()->json($data);
    });

    Route::get('missing-numbers-nc', [DocumentController::class, 'validateConsecutiveNC'])->name('electronic-billing.missing-numbers-nc');
    Route::get('missing-numbers-fe', [DocumentController::class, 'validateConsecutive'])->name('electronic-billing.missing-numbers-fe');

    Route::prefix('quality-control')->group(function () {
        Route::get('/', [QualityControlController::class, 'index']);
        Route::get('get-production-order/{production_order}', [QualityControlController::class, 'get_production_order'])->name('quality-control.get-production-order');
        Route::post('save-review', [QualityControlController::class, 'store'])->name('quality-control.save-review');
        Route::get('download-review', [QualityControlController::class, 'downloadReview'])->name('quality-control.download-review');
        Route::post('download-file', [QualityControlController::class, 'download_file'])->name('quality-control.download-file');
    });

    Route::prefix('remission')->group(function () {
        Route::get('/', [RemissionController::class, 'index']);
        Route::get('cellar', [RemissionController::class, 'cellar']);
        Route::post('/', [RemissionController::class, 'store'])->name('remission.store');
        Route::put('/{id}', [RemissionController::class, 'update'])->name('remission.update');
        Route::get('order-detail/{order}', [RemissionController::class, 'order_detail'])->name('remission.order-detail');
        Route::get('print-pdf/{id}', [RemissionController::class, 'print_pdf'])->name('remission.print-pdf');
        Route::post('close', [RemissionController::class, 'close'])->name('remission.close');
    });

    Route::resource('production-order', ProductionOrderController::class)->only('index');
    Route::post('production-order/search-data', [ProductionOrderController::class, 'search_data'])->name('production-order.search-data');
    Route::post('production-order-modal/order', [ProductionOrderController::class, 'order_modal' ])->name('order-production.order_modal');

    Route::prefix('max-update')->group(function () {
        Route::get('purchase-orders', [MaxUpdateController::class, 'purchase_orders']);
        Route::get('list-order/{oc}', [MaxUpdateController::class, 'list_order'])->name('max-update.list-order');
        Route::post('receipt-purchase-order', [MaxUpdateController::class, 'receipt_purchase_order'])->name('max-update.receipt-purchase-order');

        Route::resource('inventory-issue', InventoryIssueController::class);
        Route::get('inventory-issue/get-lots/{prtnum}', [InventoryIssueController::class, 'get_lots'])->name('max.inventory-issue.get-lots');

        Route::prefix('lot-change')->group(function () {
            Route::get('', [LotChangeController::class, 'index']);
            Route::post('replace-data', [LotChangeController::class, 'replace_data'])->name('max.lot-change.replace-data');
        });

        Route::prefix('production-orders')->group(function () {
            Route::get('', [MaxUpdateController::class, 'production_orders']);
            Route::get('/view/{order}', [MaxUpdateController::class, 'production_order_view'])->name('max-update.production-orders.view');
            Route::post('register', [MaxUpdateController::class, 'production_order_register'])->name('max-update.production-orders.register');
        });


        Route::prefix('post-operation-completion')->group(function (){
            Route::get('', [PostOperationCompletionController::class, 'index'])->name('post-operation-completion.index');
            Route::post('', [PostOperationCompletionController::class, 'update_operation'])->name('post-operation-completion.update-operation');
        });
    });

    Route::prefix('procedures')->group(function () {
        Route::get('upload-export-invoice-dms', [ProcedureController::class, 'upload_invoice_export_dms']);
        Route::get('upload-export-invoice-dms/search-documents', [ProcedureController::class, 'search_by_date'])->name('upload-export-invoice-dms.search-documents');
        Route::post('upload-documents-dms', [ProcedureController::class, 'upload_documents_dms'])->name('procedures.upload-documents-dms');
    });

    Route::get('export-employee-data', function () {
        return Excel::download(new ExportEmployeeData, 'employees.xlsx');
    });

    Route::prefix('automation')->group(function () {
        Route::resource('tariff-position-query', TariffPositionQueryController::class);
    });

    Route::prefix('replace-data')->group(function () {
        Route::get('', [ReplaceController::class, 'index']);
        Route::get('search-data', [ReplaceController::class, 'search'])->name('replace-data.search-data');
        Route::post('replace-data', [ReplaceController::class, 'replace_data'])->name('replace-data.store');
    });

    Route::prefix('support-document/{entity}')->group(function () {
        Route::get('/', [SupportDocumentController::class, 'index'])->name('support-document.index');
        Route::get('/create', [SupportDocumentController::class, 'create'])->name('support-document.create');
        Route::post('/', [SupportDocumentController::class, 'store'])->name('support-document.store');
        Route::get('search-provider', [SupportDocumentController::class, 'search_provider'])->name('support-document.search-provider');
        Route::get('search-product', [SupportDocumentController::class, 'search_product'])->name('support-document.search-product');
        Route::post('send-dian', [SupportDocumentController::class, 'send_dian'])->name('support-document.send-dian');
        Route::get('print/{id}', [SupportDocumentController::class, 'print'])->name('support-document.print');
        Route::post('adjust-note', [SupportDocumentController::class, 'adjust_note'])->name('support-document.adjust-note');
        Route::get('view/{id}', [SupportDocumentController::class, 'view'])->name('support-document.view');

        Route::prefix('product')->group(function () {
            Route::get('validate-description/{code}', [SupportDocumentController::class, 'product_validate_description'])->name('support-document.product.validate-description');
            Route::post('store', [SupportDocumentController::class, 'product_store'])->name('support-document.product.store');
        });
    });

    Route::prefix('labels')->group(function () {
        Route::get('', [LabelController::class, 'index'])->name('label.index');
        Route::get('search-production-order/{op}', [LabelController::class, 'search_production_order'])->name('label.search-production-order');
        Route::post('generate-label', [LabelController::class, 'generate_label'])->name('label.generate-label');
    });

    Route::prefix('claim')->group(function () {
        Route::get('', [ClaimController::class, 'index'])->name('claim.index');
        Route::post('', [ClaimController::class, 'store'])->name('claim.store');
        Route::get('/{hash}/show', [ClaimController::class, 'show'])->name('claim.show');
        Route::get('/{hash}/print', [ClaimController::class, 'print'])->name('claim.print');
        Route::post('/{hash}/update-causes', [ClaimController::class, 'update_causes'])->name('claim.update-causes');


        Route::get('create', [ClaimController::class, 'create'])->name('claim.create');
        Route::get('document-data/{document}', [ClaimController::class, 'document_data'])->name('claim.document-data');
        Route::get('get-causes/{destiny}', [ClaimController::class, 'get_causes'])->name('claim.get-causes');
        Route::post('add-comment', [ClaimController::class, 'add_comment'])->name('claim.add-comment');
        Route::post('download-file', [ClaimController::class, 'download_file'])->name('claim.download-file');
        Route::post('cancel', [ClaimController::class, 'cancel'])->name('claim.cancel');
        Route::post('send', [ClaimController::class, 'send'])->name('claim.send');
        Route::post('re-open', [ClaimController::class, 'reopen'])->name('claim.re-open');

        Route::prefix('quality')->group(function () {
            Route::get('', [ClaimController::class, 'quality'])->name('claim.quality');
            Route::post('store/{hash}', [ClaimController::class, 'quality_store'])->name('claim.quality-store');
            Route::post('refuse', [ClaimController::class, 'quality_refuse'])->name('claim.quality-refuse');
        });


        Route::prefix('cellar')->group(function () {
            Route::get('', [ClaimController::class, 'cellar'])->name('claim.cellar');
            Route::post('store/{hash}', [ClaimController::class, 'cellar_store'])->name('claim.cellar-store');
            Route::post('generate-credit-memo', [ClaimController::class, 'generate_credit_memo'])->name('claim.cellar-generate-credit-memo');
            Route::post('generate-remission', [ClaimController::class, 'generate_remission'])->name('claim.cellar-generate-remission');
            Route::post('generate-sale-order', [ClaimController::class, 'generate_sale_order'])->name('claim.cellar-generate-sale-order');
            Route::post('refuse', [ClaimController::class, 'cellar_refuse'])->name('claim.cellar-refuse');
            Route::post('store-without-gestion/{hash}', [ClaimController::class, 'cellar_store_without_gestion'])->name('claim.cellar-store-without-gestion');
        });

        Route::prefix('wallet')->group(function () {
            Route::get('', [ClaimController::class, 'wallet'])->name('claim.wallet');
            Route::post('store/{hash}', [ClaimController::class, 'wallet_store'])->name('claim.wallet-store');
            Route::post('store-without-gestion/{hash}', [ClaimController::class, 'wallet_store_without_gestion'])->name('claim.wallet-store-without-gestion');
        });

        Route::prefix('cost')->group(function () {
            Route::get('', [ClaimController::class, 'cost'])->name('claim.cost');
            Route::post('store/{hash}', [ClaimController::class, 'cost_store'])->name('claim.cost-store');
            Route::post('refuse/{hash}', [ClaimController::class, 'cost_refuse'])->name('claim.cost-refuse');
        });
    });

    Route::prefix('galvano-bath-parameters')->group(function () {
        Route::get('', [GalvanoBathParameterController::class, 'index'])->name('galvano-bath-parameter.index');
        Route::get('register', [GalvanoBathParameterController::class, 'register'])->name('galvano-bath-parameter.register');
        Route::get('search-op', [GalvanoBathParameterController::class, 'search_op'])->name('galvano-bath-parameter.search-op');
        Route::post('store', [GalvanoBathParameterController::class, 'store'])->name('galvano-bath-parameter.store');
    });

    Route::prefix('point-of-sale-remission')->group(function () {
        Route::get('', [PointOfSaleRemissionController::class, 'index'])->name('point-of-sale-remission.index');
        Route::put('', [PointOfSaleRemissionController::class, 'update'])->name('point-of-sale-remission.update');
        Route::get('{consecutive}/edit', [PointOfSaleRemissionController::class, 'edit'])->name('point-of-sale-remission.edit');
        Route::get('{consecutive}/print', [PointOfSaleRemissionController::class, 'print'])->name('point-of-sale-remission.print');
        Route::get('{consecutive}/view', [PointOfSaleRemissionController::class, 'view'])->name('point-of-sale-remission.view');
        Route::post('check-remission', [PointOfSaleRemissionController::class, 'check'])->name('point-of-sale-remission.check-remission');
    });

    Route::prefix('{entity}/import-document-max-dms')->group(function () {
        Route::get('', [ImportDocumentMaxDmsController::class, 'index']);
        Route::get('search', [ImportDocumentMaxDmsController::class, 'search'])->name('import-document-max-dms.search');
        Route::post('import-documents', [ImportDocumentMaxDmsController::class, 'import_documents'])->name('import-document-max-dms.import-documents');
        Route::post('activate-desactivate', [ImportDocumentMaxDmsController::class, 'activate_desactivate'])->name('import-document-max-dms.activate-desactivate');
    });

    Route::prefix('supplier-purchases')->group(function () {
        Route::get('', [SupplierPurchaseController::class, 'index'])->name('supplier-purchases.index');
        Route::post('import', [SupplierPurchaseController::class, 'import'])->name('supplier-purchases.import');
        Route::post('download-file', [SupplierPurchaseController::class, 'download_file'])->name('supplier-purchases.download-file');
        Route::post('change-state', [SupplierPurchaseController::class, 'change_state'])->name('supplier-purchases.change-state');
        Route::post('notify-reception', [SupplierPurchaseController::class, 'notify_reception'])->name('supplier-purchases.notify-reception');

        Route::prefix('work-center')->group(function () {
            Route::get('', [SupplierPurchaseController::class, 'work_center'])->name('supplier-purchases.work-center');
        });

        Route::prefix('audit')->group(function () {
            Route::get('', [SupplierPurchaseController::class, 'audit'])->name('supplier-purchases.audit');
            Route::post('check', [SupplierPurchaseController::class, 'audit_check'])->name('supplier-purchases.audit-check');
        });

        Route::get('view-pdf/{file}', [SupplierPurchaseController::class, 'view_pdf'])->name('supplier-purchases.view-pdf');
    });

    Route::prefix('check-mobility')->group(function (){
        Route::get('', [CheckMobilityController::class, 'index'])->name('check-mobility.index');
        Route::get('create', [CheckMobilityController::class, 'create'])->name('check-mobility.create');
        Route::post('', [CheckMobilityController::class, 'store'])->name('check-mobility.store');
        Route::get('{id}/show', [CheckMobilityController::class, 'show'])->name('check-mobility.show');
    });

    Route::prefix('reports')->group(function () {
        Route::get('', [ProductionReportController::class, 'index'])->name('reports.index');
        Route::prefix('production')->group(function () {
            Route::get('galvano-1', [ProductionReportController::class, 'galvano1'])->name('report-production.galvano-1');
            Route::get('galvano-1-pdf/{sortBy}', [ProductionReportController::class, 'galvano1_pdf'])->name('report-production.galvano-1-pdf');
            Route::get('galvano-2', [ProductionReportController::class, 'galvano2'])->name('report-production.galvano-2');
            Route::get('galvano-2-pdf/{sortBy}', [ProductionReportController::class, 'galvano2_pdf'])->name('report-production.galvano-2-pdf');
            Route::get('estatica', [ProductionReportController::class, 'estatica'])->name('report-production.estatica');
            Route::get('estatica-pdf/{sortBy}', [ProductionReportController::class, 'estatica_pdf'])->name('report-production.estatica-pdf');

            Route::get('cnc', [ProductionReportController::class, 'cnc'])->name('report-production.cnc');
            Route::get('zamac', [ProductionReportController::class, 'zamac'])->name('report-production.zamac');
            Route::get('laser', [ProductionReportController::class, 'laser'])->name('report-production.laser');
            Route::get('inspeccion-empaque', [ProductionReportController::class, 'inspeccion_empaque'])->name('report-production.inspeccion-empaque');
            Route::get('uv', [ProductionReportController::class, 'uv'])->name('report-production.uv');

            Route::get('pulido', [ProductionReportController::class, 'pulido'])->name('report-production.pulido');
            Route::get('pulido-pdf/{sortBy}', [ProductionReportController::class, 'pulido_pdf'])->name('report-production.pulido-pdf');

            Route::get('control-entregas-pulido', [ProductionReportController::class, 'control_entregas_pulido'])->name('report-production.control-entrega-pulido');
            Route::get('control-entregas-pulido-pdf', [ProductionReportController::class, 'generate_polished_delivery_control_pdf'])->name('report-production.control-entrega-pulido-pdf');

            Route::get('pendientes-automaticas', [ProductionReportController::class, 'pendientes_automaticas'])->name('report-production.pendientes-automaticas');

            Route::get('control-entregas-inyeccion', [ProductionReportController::class, 'control_entregas_inyeccion'])->name('report-production.control-entrega-inyeccion');
            Route::get('get-control-entregas-inyeccion', [ProductionReportController::class, 'get_control_entregas_inyeccion'])->name('report-production.get-control-entrega-inyeccion');

            Route::get('control-entregas-troquelado', [ProductionReportController::class, 'control_entregas_troquelado'])->name('report-production.control-entrega-troquelado');
            Route::get('get-control-entregas-troquelado', [ProductionReportController::class, 'get_control_entregas_troquelado'])->name('report-production.get-control-entrega-troquelado');

            Route::get('control-entregas-automaticas', [ProductionReportController::class, 'control_entregas_automaticas'])->name('report-production.control-entrega-automaticas');
            Route::get('get-control-entregas-automaticas', [ProductionReportController::class, 'get_control_entregas_automaticas'])->name('report-production.get-control-entrega-automaticas');
            Route::get('report-control-entregas-automaticas-pdf', [ProductionReportController::class, 'control_entregas_automaticas_pdf'])->name('report-production.control-entrega-automaticas-pdf');


            Route::post('download-pendientes-automaticas', [ProductionReportController::class, 'download_report_pendientes_automaticas'])->name('report-production.download-report.pendientes-automaticas');
            Route::post('download-report', [ProductionReportController::class, 'download_report'])->name('report-production.download-report');
            Route::get('report-control-entregas-inyeccion-pdf', [ProductionReportController::class, 'control_entregas_inyeccion_pdf'])->name('report-production.control-entregas-inyeccion-pdf');
            Route::get('report-control-entregas-troquelado-pdf', [ProductionReportController::class, 'control_entregas_troquelado_pdf'])->name('report-production.control-entrega-troquelado-pdf');

            Route::get('multi-report/{report}/{type}/{sales?}', [ProductionReportController::class, 'multi_report'])->name('report-production.multi-report');

            Route::get('report-ensamble-externo', [ProductionReportController::class, 'ensamble_externo_report'])->name('report-production.ensamble-externo');
            Route::get('report-ensamble-externo-pdf', [ProductionReportController::class, 'ensamble_externo_pdf'])->name('report-production.ensamble-externo-pdf');

            Route::get('production-order-stock', [ProductionReportController::class, 'production_order_stock'])->name('report-production.production-order-stock');
            Route::get('production-order-stock-detail', [ProductionReportController::class, 'production_order_stock_detail'])->name('report-production.production-order-stock-detail');

            Route::get('monitoring-injection', [ProductionReportController::class, 'monitoring_injection'])->name('report-production.monitoring-injection');
            Route::post('download-report-monitoring-injection', [ProductionReportController::class, 'download_report_monitoring_injection'])->name('report-production.download-report-monitoring-injection');

            Route::prefix('delivery-control')->group(function () {
                Route::get('{ct}', [ProductionReportController::class, 'delivery_control_report'])->name('delivery-control.report');
                Route::get('{ct}/{type}/report', [ProductionReportController::class, 'delivery_control_report_pdf'])->name('delivery-control.report-pdf');
            });
        });

        Route::prefix('sales/{entity}')->group(function () {
            Route::get('per-day', [SalesReportController::class, 'per_day'])->name('report-sales.per-day');
            Route::post('per-day/get-documents', [SalesReportController::class, 'per_day_get_documents'])->name('report-sales.per-day-get-documents');
            Route::post('per-day/download-report', [SalesReportController::class, 'per_day_download_report'])->name('report-sales.per-day-download-report');
            Route::get('ov-pending-per-product', [SalesReportController::class, 'ov_pending_per_product'])->name('report-sales.ov-pending-per-product.index');
            Route::get('ov-pending-per-product-search', [SalesReportController::class, 'ov_pending_per_product_search'])->name('report-sales.ov-pending-per-product.search');
        });
    });

    Route::get('max/reports/production/{view}/{type?}', [PendingController::class, 'report'])->name('max.reports.production');


    Route::prefix('reason-validation')->group(function () {
        Route::get('', [ReasonValidationController::class, 'index'])->name('reason-validation.index');
        Route::post('', [ReasonValidationController::class, 'update'])->name('reason-validation.update');
    });

    Route::prefix('invoice-edition')->group(function () {
        Route::get('', [InvoiceEditionController::class, 'index']);
        Route::post('', [InvoiceEditionController::class, 'update'])->name('invoice-edition.update');
        Route::get('search-invoice', [InvoiceEditionController::class, 'search_invoice'])->name('invoice-edition.search-invoice');
    });

    Route::get('change_bank_account', [ImportDocumentMaxDmsController::class, 'change_bank_account']);

    Route::prefix('archaic-art')->group(function () {
        Route::get('', [ArchaicArtController::class, 'index'])->name('archaic-art.index');
        Route::post('save', [ArchaicArtController::class, 'save'])->name('archaic-art.save');
        Route::get('verify-art/{art}', [ArchaicArtController::class, 'verify'])->name('archaic-art.verify-art');
    });

    Route::prefix('security-health-work')->group(function () {
        Route::get('sociodemographic', [SecurityHealthWorkController::class, 'sociodemographic'])->name('security-health-work.sociodemographic');
        Route::get('reports', [SecurityHealthWorkController::class, 'reports'])->name('security-health-work.reports');
        Route::get('sociodemographic/work-absenteeism/{document}', [SecurityHealthWorkController::class, 'work_absenteeism'])->name('security-health-work.work-absenteeism');
        Route::post('sociodemographic/reports/work-center', [SecurityHealthWorkController::class, 'work_center_report'])->name('work-health-security.report.work-center');
        Route::post('sociodemographic/reports/inability', [SecurityHealthWorkController::class, 'inability_report'])->name('work-health-security.report.inability');
        Route::post('sociodemographic/reports/diagnostic', [SecurityHealthWorkController::class, 'diagnostic_report'])->name('work-health-security.report.diagnostic');
        Route::post('sociodemographic/reports/download', [SecurityHealthWorkController::class, 'download_report'])->name('work-health-security.report.download');
        Route::post('sociodemographic/reports/export-absenteeism', [SecurityHealthWorkController::class, 'export_absenteeism'])->name('work-health-security.report.export-absenteeism');
    });

    Route::prefix('update-structure')->group(function () {
        Route::get('', [UpdateStructureController::class, 'index'])->name('update-structure.index');
        Route::get('search/{type?}', [UpdateStructureController::class, 'search'])->name('update-structure.search');
        Route::post('update', [UpdateStructureController::class, 'update'])->name('update-structure.update');

    });

    Route::prefix('account-receivable')->group(function () {
        Route::get('', [AccountReceivableController::class, 'index'])->name('account-receivable.index');
        Route::post('', [AccountReceivableController::class, 'download'])->name('account-receivable.download');
    });

    Route::prefix('postmark')->group(function () {
        Route::get('', [PostmarkController::class, 'index']);
    });

    Route::prefix('production-sheet')->group(function () {
        Route::get('', [ProductionSheetController::class, 'index'])->name('production-sheet.index');
        Route::post('pdf', [ProductionSheetController::class, 'pdf'])->name('production-sheet.pdf');
    });

    Route::get('data-test', [SupplierPurchaseController::class, 'test']);


    Route::prefix('plant-transaction')->group(function () {
        Route::get('', [PlantTransactionController::class, 'index'])->name('plant-transaction.index');
        Route::get('search', [PlantTransactionController::class, 'search'])->name('plant-transaction.search');
    });

    Route::prefix('op-ov-relationship')->group(function () {
        Route::get('', [OPOVRelationshipController::class, 'index'])->name('op-ov-relationship.index');
        Route::get('search', [OPOVRelationshipController::class, 'search'])->name('op-ov-relationship.search');
        Route::post('store', [OPOVRelationshipController::class, 'store'])->name('op-ov-relationship.store');
    });
});

Route::resource('employee-requests', EmployeeRequestController::class)->only('index');
Route::get('employee-requests/view-pdf', [EmployeeRequestController::class, 'view_pdf']);
Route::get('employee-requests/employee-info', [EmployeeRequestController::class, 'info_employee']);
Route::get('employee-requests/boss-info', [EmployeeRequestController::class, 'info_boss']);
Route::post('employee-requests/save-request', [EmployeeRequestController::class, 'save_request'])->name('employee-requests.save-request');
Route::post('employee-requests/vacation-request', [EmployeeRequestController::class, 'save_vacation_request'])->name('employee-requests.save-vacation-request');

Route::get('employee-requests/vacation-request/accept-request/{boss_document}/{request_id}/{employee_document}', [EmployeeRequestController::class, 'accept_vacation_request']);
Route::get('employee-requests/vacation-request/refuse-request/{boss_document}/{request_id}/{employee_document}', [EmployeeRequestController::class, 'refuse_vacation_request']);
Route::post('employee-requests/vacation-request/refuse-request/observation', [EmployeeRequestController::class, 'refuse_vacation_request_observation'])->name('employee-requests-vacation-request-refuse-request-observation');

Route::get('average-product/{product}', [ProductController::class, 'average_product']);

Route::prefix('guest')->group(function () {
    Route::get('codes', [GuestController::class, 'codes'])->name('guest.codes');
    Route::get('arts', [GuestController::class, 'arts'])->name('guest.arts');
});


Route::get('users-list', [UserController::class, 'users_list']);
Route::post('ldap-reset-password', [LdapUserController::class, 'reset_password'])->name('ldap-reset-password');
