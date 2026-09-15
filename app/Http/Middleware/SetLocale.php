<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['az', 'ru', 'en'];

        if ($request->has('lang') && in_array($request->query('lang'), $supportedLocales, true)) {
            $locale = $request->query('lang');
            session(['locale' => $locale]);
        } elseif (session()->has('locale') && in_array(session('locale'), $supportedLocales, true)) {
            $locale = session('locale');
        } else {
            // Auto detect from browser / device
            $browserLang = strtolower($request->server('HTTP_ACCEPT_LANGUAGE', ''));
            if (str_contains($browserLang, 'az')) {
                $locale = 'az';
            } elseif (str_contains($browserLang, 'ru')) {
                $locale = 'ru';
            } elseif (str_contains($browserLang, 'en')) {
                $locale = 'en';
            } else {
                $locale = 'en'; // Default English for other languages
            }
            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
