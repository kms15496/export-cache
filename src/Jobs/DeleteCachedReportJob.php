<?php

namespace Kms\ReportCache\Jobs;

use Kms\ReportCache\Models\CachedReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCachedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(): void
    {
        $report = CachedReport::find($this->id);

        if (! $report) return;

        $report->clearMediaCollection(config('report-cache.media_collection'));
        $report->delete();
    }
}
