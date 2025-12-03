
<?php

namespace Kms\ReportCache;

use Illuminate\Support\ServiceProvider;

class ReportCacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/report-cache.php', 'report-cache');

        $this->app->singleton('report-cache', function () {
            return new ReportCacheManager();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/report-cache.php' => config_path('report-cache.php'),
        ], 'config');

        if (! class_exists('CreateCachedReportsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_cached_reports_table.php'
                => database_path('migrations/'.date('Y_m_d_His').'_create_cached_reports_table.php'),
            ], 'migrations');
        }
    }
}
