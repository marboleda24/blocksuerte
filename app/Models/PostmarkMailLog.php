<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostmarkMailLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'Server', 'MessageID', 'MessageStream', 'To', 'Cc', 'Bcc', 'Recipients', 'ReceivedAt',
        'From', 'Subject', 'Attachments', 'Status', 'TrackOpens', 'MessageEvents',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'To' => 'array',
        'Cc' => 'array',
        'Bcc' => 'array',
        'Recipients' => 'array',
        'ReceivedAt' => 'datetime:Y-m-d h:i:s A',
        'Attachments' => 'array',
        'MessageEvents' => 'array',
    ];
}
