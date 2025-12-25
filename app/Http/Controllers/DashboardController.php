<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $today = now()->toDateString();

        // =========================
        // Work Orders
        // =========================
        $openWorkOrdersCount = WorkOrder::where('status', '!=', 'Completed')->count();

        $criticalOpenCount = WorkOrder::where('priority', 'Critical')
            ->where('status', '!=', 'Completed')
            ->count();

        $overdueCount = WorkOrder::whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->where('status', '!=', 'Completed')
            ->count();

        $recentOpenWorkOrders = WorkOrder::where('status', '!=', 'Completed')
            ->orderByRaw("CASE
                WHEN priority = 'Critical' THEN 1
                WHEN priority = 'High' THEN 2
                WHEN priority = 'Medium' THEN 3
                WHEN priority = 'Low' THEN 4
                ELSE 5 END")
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // =========================
        // Inspections
        // =========================
        $inspectionsTodayCount = Inspection::whereDate('inspection_date', $today)->count();

        $recentInspections = Inspection::with('inspector')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $todayInspections = Inspection::with('inspector')
            ->whereDate('inspection_date', $today)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $missingInspectionToday = $inspectionsTodayCount === 0;

        // =========================
        // Weather (METAR / TAF) + ATIS link (LiveATC)
        // =========================
        $station = 'KAVL';

        $liveAtcAtisUrl = 'https://www.liveatc.net/hlisten.php?icao=kavl&mount=kavl2_atis';
        $liveAtcAllFeedsUrl = 'https://www.liveatc.net/search/?icao=KAVL';

        $wx = Cache::remember("wx:{$station}", now()->addSeconds(90), function () use ($station) {
            $base = 'https://aviationweather.gov/api/data';

            $http = Http::withHeaders([
                'User-Agent' => 'AirportOps/1.0 (Asheville Regional Airport Ops Dashboard)',
                'Accept'     => 'application/json',
            ])->timeout(8);

            $metar = null;
            $taf   = null;

            $metarError = null;
            $tafError   = null;

            // ---- METAR (most recent) ----
            $m = $http->get("{$base}/metar", [
                'ids'        => $station,
                'format'     => 'json',
                'mostRecent' => 'true',
            ]);

            // TEMP DEBUG LOGS (remove once you're satisfied)
            Log::info('METAR status', [
                'station' => $station,
                'status'  => $m->status(),
                'body'    => $m->body(),
            ]);

            if ($m->status() === 204) {
                $metarError = "METAR: No content (204).";
            } elseif (!$m->ok()) {
                $metarError = "METAR: HTTP {$m->status()}";
            } else {
                $arr = $m->json();
                if (is_array($arr) && count($arr) > 0) {
                    // newest by obsTime (epoch seconds)
                    usort($arr, fn ($a, $b) => (int)($b['obsTime'] ?? 0) <=> (int)($a['obsTime'] ?? 0));
                    $metar = $arr[0] ?? null;
                } else {
                    $metarError = "METAR: Empty JSON payload.";
                }
            }

            // ---- TAF (most recent) ----
            $t = $http->get("{$base}/taf", [
                'ids'        => $station,
                'format'     => 'json',
                'mostRecent' => 'true',
            ]);

            // TEMP DEBUG LOGS (remove once you're satisfied)
            Log::info('TAF status', [
                'station' => $station,
                'status'  => $t->status(),
                'body'    => $t->body(),
            ]);

            if ($t->status() === 204) {
                $tafError = "TAF: No content (204).";
            } elseif (!$t->ok()) {
                $tafError = "TAF: HTTP {$t->status()}";
            } else {
                $arr = $t->json();
                if (is_array($arr) && count($arr) > 0) {
                    // newest by issueTime (epoch seconds)
                    usort($arr, fn ($a, $b) => (int)($b['issueTime'] ?? 0) <=> (int)($a['issueTime'] ?? 0));
                    $taf = $arr[0] ?? null;
                } else {
                    $tafError = "TAF: Empty JSON payload.";
                }
            }

            return [
                'metar'       => $metar,
                'taf'         => $taf,
                'metar_error' => $metarError,
                'taf_error'   => $tafError,
                'fetched_at'  => now()->toIso8601String(),
            ];
        });

        $metar = $wx['metar'] ?? null;
        $taf   = $wx['taf'] ?? null;

        // ✅ Correct keys from your AWC JSON
        $metarRaw = $metar['rawOb'] ?? null;
        $tafRaw   = $taf['rawTAF'] ?? null;

        $fltCat   = $metar['fltCat'] ?? null;

        // Epoch seconds → Carbon
        $metarEpoch = $metar['obsTime'] ?? null;
        $tafEpoch   = $taf['issueTime'] ?? null;

        $metarTime = $metarEpoch ? Carbon::createFromTimestampUTC((int) $metarEpoch) : null;
        $tafTime   = $tafEpoch   ? Carbon::createFromTimestampUTC((int) $tafEpoch)   : null;

        $wxMetarError = $wx['metar_error'] ?? null;
        $wxTafError   = $wx['taf_error'] ?? null;

        // =========================
        // Decoded Weather UI bundles (for pretty dashboard rendering)
        // =========================

        // METAR decoded
        $windDir  = $metar['wdir'] ?? null;   // degrees
        $windSpd  = $metar['wspd'] ?? null;   // kt
        $windGst  = $metar['wgst'] ?? null;   // kt
        $visib    = $metar['visib'] ?? null;  // "10+"
        $tempC    = $metar['temp'] ?? null;   // C
        $dewpC    = $metar['dewp'] ?? null;   // C
        $altimHpa = $metar['altim'] ?? null;  // hPa
        $cover    = $metar['cover'] ?? null;  // CLR/FEW/SCT/BKN/OVC

        $altimInHg = is_numeric($altimHpa)
            ? round(((float) $altimHpa) * 0.0295299830714, 2)
            : null;

        $clouds = $metar['clouds'] ?? [];

        // Ceiling = lowest BKN/OVC base (ft AGL)
        $ceilingFt = null;
        if (is_array($clouds)) {
            foreach ($clouds as $c) {
                $cCover = $c['cover'] ?? null;
                $base   = $c['base'] ?? null;
                if (in_array($cCover, ['BKN', 'OVC'], true) && is_numeric($base)) {
                    $ceilingFt = $ceilingFt === null ? (int) $base : min($ceilingFt, (int) $base);
                }
            }
        }

        $metarUi = [
            'windDir'   => $windDir,
            'windSpd'   => $windSpd,
            'windGst'   => $windGst,
            'visib'     => $visib,
            'tempC'     => $tempC,
            'dewpC'     => $dewpC,
            'altimHpa'  => $altimHpa,
            'altimInHg' => $altimInHg,
            'cover'     => $cover,
            'ceilingFt' => $ceilingFt,
            'clouds'    => $clouds,
        ];

        // TAF decoded timeline (next 4 forecast periods)
        $tafFcsts = is_array($taf['fcsts'] ?? null) ? $taf['fcsts'] : [];

        $tafTimeline = collect($tafFcsts)->take(4)->map(function ($f) {
            return [
                'from'   => $f['timeFrom'] ?? null,
                'to'     => $f['timeTo'] ?? null,
                'change' => $f['fcstChange'] ?? null, // FM, etc.
                'wind'   => [
                    'dir' => $f['wdir'] ?? null,
                    'spd' => $f['wspd'] ?? null,
                    'gst' => $f['wgst'] ?? null,
                ],
                'visib'  => $f['visib'] ?? null,
                'wx'     => $f['wxString'] ?? null,
                'clouds' => $f['clouds'] ?? [],
            ];
        })->values()->all();

        return view('dashboard', compact(
            'openWorkOrdersCount',
            'criticalOpenCount',
            'overdueCount',
            'recentOpenWorkOrders',
            'inspectionsTodayCount',
            'recentInspections',
            'todayInspections',
            'missingInspectionToday',

            'station',
            'metar',
            'taf',
            'metarRaw',
            'tafRaw',
            'metarUi',
            'tafTimeline',
            'fltCat',
            'metarTime',
            'tafTime',
            'wxMetarError',
            'wxTafError',
            'liveAtcAtisUrl',
            'liveAtcAllFeedsUrl'
        ));
    }
}
