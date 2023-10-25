<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceAssetFile extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'asset_id', 'path',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
        'base64_file'
    ];

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return  substr($this->path, strrpos($this->path, '/') + 1);
    }

    /**
     * @return string
     */
    public function getBase64FileAttribute(): string
    {
        $file = Storage::get($this->path);
        $mime = Storage::mimeType($this->path);

        return "data:$mime;base64,".base64_encode($file);
    }
}
