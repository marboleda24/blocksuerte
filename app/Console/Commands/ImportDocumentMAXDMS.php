<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;

class ImportDocumentMAXDMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-documents:disabled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'activate or desactivate automatic import by electronic invoice';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        SystemSetting::find(1)
            ->update([
                'automatic_import_DMS_MAX' => false,
            ]);

        SystemSetting::find(2)
            ->update([
                'automatic_import_DMS_MAX' => false,
            ]);

        return true;
    }
}
