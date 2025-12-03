
<?php

namespace Kms\ReportCache\Facades;

use Illuminate\Support\Facades\Facade;

class ReportCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'report-cache';
    }
}
