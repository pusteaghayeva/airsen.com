<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnalyticsController extends Controller
{
    /**
     * Track client-side interactions and page views.
     */
    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', Rule::in([
                'page_view',
                'google_play_click',
                'simulator_interaction',
                'language_switched',
                'review_modal_opened',
                'beta_modal_opened',
            ])],
            'language' => ['nullable', Rule::in(['az', 'ru', 'en'])],
            'page_url' => ['nullable', 'string', 'max:255', 'regex:/^\/(?!\/)/'],
        ]);

        $ip = $request->ip();
        $ipHash = hash('sha256', $ip.date('Y-m-d'));
        $userAgent = $request->userAgent() ?? '';
        $eventType = $validated['event_type'];
        $language = $validated['language'] ?? app()->getLocale();
        $pageUrl = $validated['page_url'] ?? '/';

        $browser = $this->detectBrowser($userAgent);
        $os = $this->detectOS($userAgent);
        $device = $this->detectDevice($userAgent);
        $country = $this->detectCountry($request);

        Visit::create([
            'ip_hash' => $ipHash,
            'event_type' => $eventType,
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
            'country' => $country,
            'language' => $language,
            'user_agent' => substr($userAgent, 0, 500),
            'page_url' => substr($pageUrl, 0, 255),
        ]);

        return response()->json(['status' => 'recorded']);
    }

    private function detectCountry(Request $request): string
    {
        $countryCode = strtoupper((string) $request->header('CF-IPCountry', ''));

        return preg_match('/^[A-Z]{2}$/', $countryCode) === 1 ? $countryCode : 'Direct / Unknown';
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/')) {
            return 'Microsoft Edge';
        }
        if (str_contains($ua, 'Chrome/')) {
            return 'Google Chrome';
        }
        if (str_contains($ua, 'Safari/') && ! str_contains($ua, 'Chrome/')) {
            return 'Apple Safari';
        }
        if (str_contains($ua, 'Firefox/')) {
            return 'Mozilla Firefox';
        }
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera/')) {
            return 'Opera';
        }

        return 'Other';
    }

    private function detectOS(string $ua): string
    {
        if (str_contains($ua, 'Windows')) {
            return 'Windows';
        }
        if (str_contains($ua, 'Android')) {
            return 'Android';
        }
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            return 'iOS';
        }
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) {
            return 'macOS';
        }
        if (str_contains($ua, 'Linux')) {
            return 'Linux';
        }

        return 'Other';
    }

    private function detectDevice(string $ua): string
    {
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone')) {
            return 'Mobile';
        }
        if (str_contains($ua, 'iPad') || str_contains($ua, 'Tablet')) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
