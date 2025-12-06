<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VolcanoRealtimeController extends Controller
{
    public function index()
    {
        return view('volcano.realtime');
    }

    // fetch latest volcano data from Ambee API
    public function latest(Request $request)
    {
        $continent = $request->query('continent', 'EUR');
        $limit     = (int) $request->query('limit', 10);
        $page      = (int) $request->query('page', 1);
        $useFallback = $request->boolean('fallback', true); // default: true

        $apiKey = config('services.ambee.key');

        if (empty($apiKey)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ambee API key is not configured (missing AMBEE_API_KEY in .env).',
            ], 500);
        }

        try {
            $client = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'x-api-key'    => $apiKey,
                'Content-type' => 'application/json',
            ]);

            // 1) volcano events only
            $primaryResponse = $client->get('https://api.ambeedata.com/disasters/latest/by-continent', [
                'continent' => $continent,
                'eventType' => 'VO',
                'limit'     => $limit,
                'page'      => $page,
            ]);

            if ($primaryResponse->failed()) {
                $body = $primaryResponse->json();

                Log::warning('Ambee volcano API primary request failed', [
                    'status' => $primaryResponse->status(),
                    'body'   => $body,
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => $body['message'] ?? 'Unable to fetch volcano data from Ambee.',
                    'details' => $body,
                ], $primaryResponse->status() ?: 500);
            }

            $primaryData  = $primaryResponse->json();
            $rawEvents    = $this->extractEvents($primaryData);
            $eventSource  = 'volcano_only';
            $fallbackData = null;

            // 2) fallback: any disasters if no volcano events
            if (empty($rawEvents) && $useFallback) {
                $fallbackResponse = $client->get('https://api.ambeedata.com/disasters/latest/by-continent', [
                    'continent' => $continent,
                    'limit'     => $limit,
                    'page'      => $page,
                ]);

                if ($fallbackResponse->ok()) {
                    $fallbackData = $fallbackResponse->json();
                    $fallbackRaw  = $this->extractEvents($fallbackData);

                    if (!empty($fallbackRaw)) {
                        $rawEvents   = $fallbackRaw;
                        $eventSource = 'fallback_natural_disasters';
                    }
                }
            }

            // normalize events 
            $events = array_map(function ($event) {
                return $this->normalizeEvent($event);
            }, $rawEvents);

            return response()->json([
                'status'        => 'ok',
                'continent'     => $continent,
                'events'        => $events,
                'event_source'  => $eventSource,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error calling Ambee volcano API', [
                'message'   => $e->getMessage(),
                'continent' => $continent,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Unexpected error while contacting Ambee.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // extract events
    private function extractEvents(?array $data): array
    {
        if (!is_array($data)) {
            return [];
        }

        if (isset($data['result']) && is_array($data['result'])) {
            return $data['result'];
        }
        if (isset($data['events']) && is_array($data['events'])) {
            return $data['events'];
        }
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
        if (isset($data['disasters']) && is_array($data['disasters'])) {
            return $data['disasters'];
        }

        return [];
    }

    private function normalizeEvent(array $event): array
    {
        $details = $event['details'] ?? $event;

        $rawName    = $details['event_name']   ?? ($event['event_name']   ?? null);
        $place      = $details['event_place']  ?? ($details['country']    ?? ($event['country'] ?? null));
        $country    = $details['country_code'] ?? ($event['country_code'] ?? null);
        $eventType  = $details['event_type']   ?? ($event['event_type']   ?? null);
        $dateRaw    = $details['event_date']   ?? ($details['start_date_and_last_detection'] ?? ($event['event_date'] ?? null));
        $magnitude  = $details['event_magnitude'] ?? ($event['event_magnitude'] ?? null);
        $severity   = $details['severity']        ?? ($event['severity']        ?? null);
        $vei        = $details['max_volcano_explosivity_index_vei'] ?? null;
        $exposedPop = $details['exposed_population'] ?? ($details['population_exposure_index_pai'] ?? null);
        $description = $details['event_description'] ?? null;

        // format date
        $dateFormatted = $dateRaw;
        if ($dateRaw) {
            try {
                $dt = new \DateTime($dateRaw);
                $dateFormatted = $dt->format('Y-m-d H:i');
            } catch (\Throwable $e) {
    
            }
        }

        // Title logic 
        if ($place) {
            $title = $place;
        } elseif ($rawName && !$this->looksLikeId($rawName)) {
            $title = $rawName;
        } elseif ($country) {
            $title = "Event in {$country}";
        } else {
            $title = 'Natural disaster event';
        }

        // Learn-more URL
        $searchParts = [];
        if ($title)   $searchParts[] = $title;
        if ($country) $searchParts[] = $country;

        $typeLabel = $this->mapEventTypeReadable($eventType);
        if ($typeLabel) {
            $searchParts[] = $typeLabel;
        }

        $learnMoreUrl = null;
        if (!empty($details['more_info']) && is_string($details['more_info'])) {
            $trimmed = trim($details['more_info']);
            if (preg_match('#^https?://#i', $trimmed)) {
                $learnMoreUrl = $trimmed;
            }
        }

        if (!$learnMoreUrl && !empty($searchParts)) {
            $learnMoreUrl = 'https://www.google.com/search?q=' . urlencode(implode(' ', $searchParts));
        }

        return [
            'title'              => $title,
            'location_label'     => $country ?: 'Location not specified',
            'country_code'       => $country,
            'date'               => $dateFormatted,
            'magnitude'          => $magnitude,
            'severity'           => $severity,
            'vei'                => $vei,
            'exposed_population' => $exposedPop,
            'description'        => $description,
            'learn_more_url'     => $learnMoreUrl,
        ];
    }

    private function looksLikeId(string $value): bool
    {
        $str = trim($value);
        if (strlen($str) < 12) {
            return false;
        }

        if (preg_match('/^[0-9a-f]+$/i', $str)) {
            return true;
        }

        if (preg_match('/^[0-9]+$/', $str)) {
            return true;
        }

        return false;
    }

    private function mapEventTypeReadable(?string $code): string
    {
        if (!$code) return '';

        return match ($code) {
            'VO' => 'volcano',
            'EQ' => 'earthquake',
            'FL' => 'flood',
            'WF' => 'wildfire',
            'TC' => 'tropical cyclone',
            'TN' => 'tsunami',
            'DR' => 'drought',
            'SW' => 'severe storm',
            'ET' => 'extreme temperature',
            'LS' => 'landslide',
            default => 'natural disaster',
        };
    }
}
