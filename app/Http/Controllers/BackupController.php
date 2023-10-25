<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:administration.backups');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        if (! auth()->user()->can('backup')) {
            abort(403, 'Unauthorized action.');
        }

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        $files = $disk->files('EVPIU-VUE');

        $backups = [];
        // make an array of backup files, with their filesize and creation date
        foreach ($files as $k => $f) {
            // only take the zip files into account
            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $backups[] = [
                    'file_name' => str_replace('EVPIU-VUE/', '', $f),
                    'file_size' => $this->formatSizeUnits($disk->size($f)),
                    'last_modified' => Carbon::createFromTimestamp($disk->lastModified($f))->diffForHumans(),
                ];
            }
        }
        // reverse the backups, so the newest one would be on top
        $backups = array_reverse($backups);

        return Inertia::render('Applications/Backup', [
            'data' => $backups,
        ]);
    }

    /**
     * Create a resource.
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        try {
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();

            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n".$output);

            $output = ['success' => 1,
                'msg' => __('Backup creado con exito!'),
            ];
        } catch (\Exception $e) {
            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return back()->with('status', $output);
    }

    /*
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    /**
     * @throws FilesystemException
     */
    public function download($file_name): StreamedResponse
    {
        if (! auth()->user()->can('backup')) {
            abort(403, 'Accion no autorizada.');
        }

        $file = config('backup.backup.name').'/'.$file_name;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists($file)) {
            $fs = Storage::disk(config('backup.backup.destination.disks')[0])->getDriver();
            $stream = $fs->readStream($file);

            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => $fs->getMimetype($file),
                'Content-Length' => $fs->getSize($file),
                'Content-disposition' => 'attachment; filename="'.basename($file).'"',
            ]);
        } else {
            abort(404, 'El archivo de backup no existe.');
        }
    }

    /**
     * Deletes a backup file.
     *
     * @param $file_name
     * @return RedirectResponse
     */
    public function delete($file_name): RedirectResponse
    {
        if (! auth()->user()->can('backup')) {
            abort(403, 'Accion no autorizada.');
        }

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists(config('backup.backup.name').'/'.$file_name)) {
            $disk->delete(config('backup.backup.name').'/'.$file_name);

            return redirect()->back();
        } else {
            abort(404, 'El archivo de backup no existe.');
        }
    }

    /**
     * formatSizeUnits
     *
     * @param  mixed  $bytes
     * @return string
     */
    private function formatSizeUnits($bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
