<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the AirSen landing page.
     */
    public function index(Request $request, ?string $locale = null): View
    {
        if ($locale && in_array($locale, ['az', 'ru', 'en'], true)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        } elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        } else {
            app()->setLocale('az');
            session(['locale' => 'az']);
        }

        $currentLocale = app()->getLocale();

        // Public reviews: Only approved real reviews from visitors
        $approvedReviews = Review::where('is_approved', true)
            ->latest()
            ->take(12)
            ->get();

        $googlePlayStatus = Setting::get('google_play_status', config('airsen.google_play_status', 'moderation'));
        $googlePlayUrl = Setting::get('google_play_url', config('airsen.google_play_url', 'https://play.google.com/store/apps/details?id=com.airsen.app'));

        // Prepare all language dictionaries for client-side instant toggle
        $translations = [
            'az' => trans('messages', [], 'az'),
            'ru' => trans('messages', [], 'ru'),
            'en' => trans('messages', [], 'en'),
        ];

        return view('landing', compact('approvedReviews', 'googlePlayStatus', 'googlePlayUrl', 'currentLocale', 'translations'));
    }
}
