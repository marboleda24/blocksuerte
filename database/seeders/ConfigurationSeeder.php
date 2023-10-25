<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Prefix.
     *
     * @var string
     */
    public $prefix = 'csv';

    /**
     * Tables.
     *
     * @var array
     */
    public $tables = [
        'api_type_organizations' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_events' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_countries' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_departments' => [
            'columns' => 'id, country_id, name, code, @created_at, @updated_at',
        ],
        'api_municipalities' => [
            'columns' => 'id, department_id, name, code, codefacturador, @created_at, @updated_at',
        ],
        'api_type_document_identifications' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_taxes' => [
            'columns' => 'id, name, description, code, @created_at, @updated_at',
        ],
        'api_type_regimes' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_type_liabilities' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_payment_forms' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_payment_methods' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_discounts' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_type_currencies' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_unit_measures' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_reference_prices' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_type_documents' => [
            'columns' => 'id, name, code, cufe_algorithm, prefix, @created_at, @updated_at',
        ],
        'api_type_item_identifications' => [
            'columns' => 'id, name, code, code_agency, @created_at, @updated_at',
        ],
        'api_type_operations' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_type_environments' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'api_languages' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach ($this->tables as $key => $table) {
            $rutafile = public_path($this->prefix.DIRECTORY_SEPARATOR."{$key}.{$this->prefix}");
            $rutafile = str_replace('\\', '/', $rutafile);
            DB::connection()
                ->getpdo()
                ->exec("LOAD DATA LOCAL INFILE '".$rutafile."' INTO TABLE $key({$table['columns']}) SET created_at = NOW(), updated_at = NOW()");
        }
    }
}
