<?php

namespace Kms\ReportCache\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CachedReport extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'report_type',
        'start_date',
        'end_date',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
