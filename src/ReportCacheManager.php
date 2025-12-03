
<?php

namespace Kms\ReportCache;

use Closure;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Kms\ReportCache\Models\CachedReport;
use Kms\ReportCache\Jobs\DeleteCachedReportJob;

class ReportCacheManager
{
    public function for(string $reportType, $startDate, $endDate, Closure $exportFactory): array
    {
        $start = date('Y-m-d', strtotime($startDate));
        $end   = date('Y-m-d', strtotime($endDate));

        $ttl = config('report-cache.ttl_days', 2);
        $collection = config('report-cache.media_collection', 'reports');

        // Check existing cache
        $cached = CachedReport::where('report_type', $reportType)
            ->whereDate('start_date', $start)
            ->whereDate('end_date', $end)
            ->first();

        if ($cached && $cached->expires_at > now()) {
            $media = $cached->getFirstMedia($collection);
            if ($media) {
                return [
                    'cached' => true,
                    'model' => $cached,
                    'url' => $media->getFullUrl(),
                    'media' => $media,
                ];
            }
        }

        // Generate new
        $fileName = "report_{$reportType}_{$start}_{$end}.xlsx";
        $tempPath = "tmp/{$fileName}";

        Storage::disk('local')->makeDirectory('tmp');

        Excel::store($exportFactory(), $tempPath, 'local');

        $cached = CachedReport::updateOrCreate([
            'report_type' => $reportType,
            'start_date' => $start,
            'end_date' => $end,
        ], [
            'generated_at' => now(),
            'expires_at' => now()->addDays($ttl),
        ]);

        $media = $cached
            ->addMedia(Storage::disk('local')->path($tempPath))
            ->usingFileName($fileName)
            ->toMediaCollection($collection);

        Storage::disk('local')->delete($tempPath);

        DeleteCachedReportJob::dispatch($cached->id)->delay($cached->expires_at);

        return [
            'cached' => false,
            'model' => $cached,
            'url' => $media->getFullUrl(),
            'media' => $media,
        ];
    }
}
