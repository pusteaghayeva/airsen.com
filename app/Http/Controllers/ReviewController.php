<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Setting;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Store a newly submitted review with mandatory premoderation (is_approved: false).
     */
    public function store(Request $request): JsonResponse
    {
        $locale = $request->input('locale', app()->getLocale());
        if (! in_array($locale, ['az', 'ru', 'en'], true)) {
            $locale = 'az';
        }

        $messages = [
            'az' => [
                'name.required' => 'Zəhmət olmasa ad və soyadınızı daxil edin.',
                'name.max' => 'Ad və soyad 100 simvoldan çox ola bilməz.',
                'rating.required' => 'Zəhmət olmasa qiymətləndirmə seçin.',
                'rating.numeric' => 'Qiymət düzgün rəqəm olmalıdır.',
                'rating.min' => 'Qiymət ən azı 1 olmalıdır.',
                'rating.max' => 'Qiymət ən çoxu 5.0 ola bilər.',
                'comment.required' => 'Zəhmət olmasa fikrinizi və ya rəyinizi yazın.',
                'comment.max' => 'Rəy mətni 1000 simvoldan artıq ola bilməz.',
            ],
            'ru' => [
                'name.required' => 'Пожалуйста, введите ваше имя и фамилию.',
                'name.max' => 'Имя и фамилия не должны превышать 100 символов.',
                'rating.required' => 'Пожалуйста, выберите оценку.',
                'rating.numeric' => 'Оценка должна быть числом.',
                'rating.min' => 'Минимальная оценка 1.',
                'rating.max' => 'Максимальная оценка 5.0.',
                'comment.required' => 'Пожалуйста, напишите ваш отзыв или предложение.',
                'comment.max' => 'Текст отзыва не должен превышать 1000 символов.',
            ],
            'en' => [
                'name.required' => 'Please enter your full name.',
                'name.max' => 'Name must not exceed 100 characters.',
                'rating.required' => 'Please select a rating.',
                'rating.numeric' => 'Rating must be a number.',
                'rating.min' => 'Minimum rating is 1.',
                'rating.max' => 'Maximum rating is 5.0.',
                'comment.required' => 'Please enter your review or feedback.',
                'comment.max' => 'Review comment must not exceed 1000 characters.',
            ],
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:1|max:1000',
            'locale' => 'nullable|string|in:az,ru,en',
        ], $messages[$locale] ?? $messages['az']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $review = Review::create([
            'name' => trim(strip_tags($request->input('name'))),
            'rating' => (float) $request->input('rating'),
            'comment' => trim(strip_tags($request->input('comment'))),
            'is_approved' => false, // PREMODERATION REQUIRED PER SPEC
            'locale' => $locale,
        ]);

        // Track submission event
        Visit::create([
            'ip_hash' => hash('sha256', $request->ip().date('Y-m-d')),
            'event_type' => 'review_submitted',
            'language' => $review->locale,
            'page_url' => $this->safePagePath($request->header('referer')),
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('messages.modal_success', [], $review->locale),
        ]);
    }

    /**
     * Store a beta program notification subscription and dispatch welcome notification.
     */
    public function subscribeBeta(Request $request): JsonResponse
    {
        $locale = $request->input('locale', app()->getLocale());
        if (! in_array($locale, ['az', 'ru', 'en'], true)) {
            $locale = 'az';
        }

        $betaMessages = [
            'az' => [
                'email.required' => 'Zəhmət olmasa e-poçt ünvanınızı daxil edin.',
                'email.email' => 'Düzgün e-poçt formatı daxil edin (məs: user@example.com).',
                'email.max' => 'E-poçt ünvanı 150 simvoldan çox ola bilməz.',
            ],
            'ru' => [
                'email.required' => 'Пожалуйста, укажите адрес электронной почты.',
                'email.email' => 'Введите корректный адрес эл. почты.',
                'email.max' => 'E-mail не должен превышать 150 символов.',
            ],
            'en' => [
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email must not exceed 150 characters.',
            ],
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:150',
            'locale' => 'nullable|string|in:az,ru,en',
        ], $betaMessages[$locale] ?? $betaMessages['az']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = trim(strtolower($request->input('email')));
        $locale = $request->input('locale', app()->getLocale());

        // Save email into beta waitlist settings list
        $currentList = json_decode(Setting::get('beta_waitlist', '[]'), true) ?: [];
        $isExisting = in_array($email, array_column($currentList, 'email'), true);

        if (! $isExisting) {
            array_unshift($currentList, [
                'email' => $email,
                'locale' => $locale,
                'promo_code' => 'AIRSEN-3M-VIP',
                'created_at' => now()->toDateTimeString(),
            ]);
            Setting::set('beta_waitlist', json_encode($currentList));
        }

        // Track beta submission visit
        Visit::create([
            'ip_hash' => hash('sha256', $request->ip().date('Y-m-d')),
            'event_type' => 'beta_subscribed',
            'language' => $locale,
            'page_url' => $this->safePagePath($request->header('referer')),
        ]);

        // Dispatch Welcome Email (gracefully handled via try/catch)
        try {
            $safeEmail = e($email);

            Mail::html(
                '<h2>AirSen Premium — Beta Proqramı</h2>'.
                "<p>Salam! Sizin <strong>{$safeEmail}</strong> ünvanınız AirSen Premium qapalı sınaq proqramına uğurla əlavə edildi.</p>".
                '<p><strong>Hədiyyə Kodu:</strong> AIRSEN-3M-VIP (3 Ay Pulsuz Premium Giriş)</p>'.
                '<p>Tətbiq Google Play Store və App Store-da rəsmi yayımlanan kimi sizə birbaşa aktivasiya linki göndəriləcək.</p>'.
                '<p>Təhlükəsizliyiniz bizim üçün ən vacib dəyərdir.<br>— AirSen Komandası</p>',
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('AirSen Premium Beta — 3 Ay Pulsuz İstifadə Qeydiyyatı');
                }
            );
        } catch (\Throwable $e) {
            // Non-blocking log
            Log::warning('Beta subscriber welcome email could not be sent.', [
                'exception' => $e::class,
            ]);
        }

        $titles = [
            'az' => 'Təbriklər! Beta Siyahısına Qoşuldunuz 🎉',
            'ru' => 'Поздравляем! Вы в списке закрытого бета-теста 🎉',
            'en' => 'Congratulations! You are on the Beta Waitlist 🎉',
        ];

        return response()->json([
            'success' => true,
            'email' => $email,
            'promo_code' => 'AIRSEN-3M-VIP',
            'title' => $titles[$locale] ?? $titles['az'],
            'message' => trans('messages.beta_success_text', [], $locale),
        ]);
    }

    private function safePagePath(?string $referer): string
    {
        if (! $referer) {
            return '/';
        }

        $path = parse_url($referer, PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, '/')) {
            return '/';
        }

        return substr($path, 0, 255);
    }
}
