<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🏰 Skip tracking if not installed
        if (!file_exists(storage_path('installed.lock'))) {
            return $next($request);
        }

        $response = $next($request);

        // Don't track admin panel, livewire updates, or non-GET requests
        if ($request->is('admin*') || $request->header('X-Livewire') || !$request->isMethod('get')) {
            return $response;
        }

        // Don't track static files or internal inertia calls if needed, 
        // but inertia requests ARE page views in SPAs. Let's just avoid obvious noise.
        if ($request->expectsJson() && !$request->header('X-Inertia')) {
            return $response;
        }

        try {
            $ip = $request->ip();
            $ua = $request->userAgent();
            $url = $request->fullUrl();

            // Unique visit check (once per session/day)
            $sessionKey = 'visited_' . md5($ip . $ua . date('Y-m-d'));
            $isUnique = false;

            if (!Session::has($sessionKey)) {
                Session::put($sessionKey, true);
                $isUnique = true;
            }

            $countryCode = null;
            $countryName = null;
            $cityName = null;

            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                try {
                    $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
                    if (file_exists($dbPath)) {
                        $reader = new \GeoIp2\Database\Reader($dbPath);
                        $record = $reader->city($ip);
                        
                        $countryCode = $record->country->isoCode;
                        $countryName = $record->country->name;
                        $cityName = $record->city->name;
                    }
                } catch (\Exception $geoEx) {
                    // Suppress geo ip parsing errors
                }
            } else {
                // Testing locally
                $countryCode = 'ID';
                $countryName = 'Indonesia';
                $cityName = 'Localhost';
            }

            SiteVisit::create([
                'ip_address'   => $ip,
                'country_code' => $countryCode,
                'country'      => $countryName,
                'city'         => $cityName,
                'user_agent'   => $ua,
                'url'          => $url,
                'referer'      => $request->headers->get('referer'),
                'is_unique'    => $isUnique,
            ]);
        } catch (\Exception $e) {
            // Silently fail to not break the user experience
        }

        return $response;
    }
}
