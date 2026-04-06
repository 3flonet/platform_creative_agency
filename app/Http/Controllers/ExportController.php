<?php

namespace App\Http\Controllers;

use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function trafficCsv(Request $request): StreamedResponse
    {
        $period = $request->get('period', '30');
        $now = now();

        $query = SiteVisit::query();

        if ($period === 'today') {
            $query->whereDate('created_at', $now->toDateString());
            $filename = 'traffic-today-' . $now->format('Y-m-d') . '.csv';
        } elseif ($period === 'yesterday') {
            $query->whereDate('created_at', $now->copy()->subDay()->toDateString());
            $filename = 'traffic-yesterday-' . $now->copy()->subDay()->format('Y-m-d') . '.csv';
        } elseif ($period === '7') {
            $query->where('created_at', '>=', $now->copy()->subDays(6)->startOfDay());
            $filename = 'traffic-last-7-days.csv';
        } elseif ($period === '30') {
            $query->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay());
            $filename = 'traffic-last-30-days.csv';
        } elseif ($period === 'this_month') {
            $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
            $filename = 'traffic-' . $now->format('Y-m') . '.csv';
        } else {
            $filename = 'traffic-all-time.csv';
        }

        $visits = $query->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($visits) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, ['Date', 'URL', 'IP Address', 'Browser', 'Referrer', 'Unique Visit']);

            foreach ($visits as $visit) {
                fputcsv($handle, [
                    $visit->created_at->format('Y-m-d H:i:s'),
                    $visit->url,
                    $visit->ip_address,
                    $this->parseBrowser($visit->user_agent),
                    $visit->referer ?: '-',
                    $visit->is_unique ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function parseBrowser(?string $ua): string
    {
        if (!$ua) return 'Unknown';
        if (stripos($ua, 'Edg') !== false) return 'Edge';
        if (stripos($ua, 'Chrome') !== false) return 'Chrome';
        if (stripos($ua, 'Firefox') !== false) return 'Firefox';
        if (stripos($ua, 'Safari') !== false) return 'Safari';
        return 'Other';
    }
}
