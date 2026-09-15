<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard or Login view.
     */
    public function index(Request $request): View
    {
        if (! Auth::check()) {
            return view('admin.login');
        }

        $currentUser = Auth::user();
        $canViewAnalytics = $currentUser->isAnalyst();
        $canManageReviews = $currentUser->isModerator();
        $canManageSystem = $currentUser->isSuperAdmin();

        // Metrics
        $totalVisits = $canViewAnalytics ? Visit::where('event_type', 'page_view')->count() : 0;
        $uniqueVisitors = $canViewAnalytics ? Visit::where('event_type', 'page_view')->distinct('ip_hash')->count('ip_hash') : 0;
        $playStoreClicks = $canViewAnalytics ? Visit::where('event_type', 'google_play_click')->count() : 0;
        $simulatorRuns = $canViewAnalytics ? Visit::where('event_type', 'simulator_interaction')->count() : 0;
        $totalReviews = $canManageReviews ? Review::count() : 0;
        $approvedReviewsCount = $canManageReviews ? Review::where('is_approved', true)->count() : 0;
        $pendingReviewsCount = $canManageReviews ? Review::where('is_approved', false)->count() : 0;

        // 7-Day Traffic Dynamics for Chart.js
        $chartLabels = [];
        $chartPageviews = [];
        $chartVisitors = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $chartPageviews[] = $canViewAnalytics
                ? Visit::where('event_type', 'page_view')->whereBetween('created_at', [$dayStart, $dayEnd])->count()
                : 0;
            $chartVisitors[] = $canViewAnalytics
                ? Visit::where('event_type', 'page_view')->whereBetween('created_at', [$dayStart, $dayEnd])->distinct('ip_hash')->count('ip_hash')
                : 0;
        }

        // Language stats
        $languageStats = Visit::select('language', DB::raw('count(*) as total'))
            ->when(! $canViewAnalytics, fn ($query) => $query->whereRaw('1 = 0'))
            ->whereNotNull('language')
            ->groupBy('language')
            ->orderByDesc('total')
            ->get();

        // Normalized Device stats
        $deviceStats = Visit::select(
            DB::raw("
                CASE 
                    WHEN LOWER(device) LIKE '%mobile%' OR LOWER(device) LIKE '%phone%' THEN 'Smartfon / Mobil'
                    WHEN LOWER(device) LIKE '%tablet%' OR LOWER(device) LIKE '%ipad%' THEN 'Planşet'
                    ELSE 'Kompüter / Noutbuk'
                END as device_name
            "),
            DB::raw('count(*) as total')
        )
            ->when(! $canViewAnalytics, fn ($query) => $query->whereRaw('1 = 0'))
            ->groupBy('device_name')
            ->orderByDesc('total')
            ->get();

        // Paginated Live Visits (20 per page)
        $recentVisits = Visit::latest()
            ->when(! $canViewAnalytics, fn ($query) => $query->whereRaw('1 = 0'))
            ->paginate(20, ['*'], 'visits_page')
            ->withQueryString()
            ->fragment('traffic');

        // Paginated Reviews (10 per page)
        $pendingReviews = Review::where('is_approved', false)
            ->when(! $canManageReviews, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString()
            ->fragment('reviews');

        $approvedReviews = Review::where('is_approved', true)
            ->when(! $canManageReviews, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->paginate(10, ['*'], 'approved_page')
            ->withQueryString()
            ->fragment('reviews');

        // Paginated Admin Users (10 per page) - Team order preserved as requested
        $adminUsers = User::orderBy('id', 'asc')
            ->when(! $canManageSystem, fn ($query) => $query->whereKey($currentUser->id))
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString()
            ->fragment('team');

        // Beta Waitlist Subscribers (Paginated 10 per page - Guaranteed Newest First by Date!)
        $rawBeta = $canManageSystem ? (json_decode(Setting::get('beta_waitlist', '[]'), true) ?: []) : [];
        usort($rawBeta, function ($a, $b) {
            $timeA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
            $timeB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;

            return $timeB <=> $timeA; // Newest first
        });
        $betaSubscribersCount = count($rawBeta);
        $betaPage = (int) $request->input('beta_page', 1);
        $betaPerPage = 10;
        $betaSlice = array_slice($rawBeta, ($betaPage - 1) * $betaPerPage, $betaPerPage);
        $betaWaitlist = new LengthAwarePaginator(
            $betaSlice,
            $betaSubscribersCount,
            $betaPerPage,
            $betaPage,
            [
                'path' => route('admin.index'),
                'query' => $request->query(),
                'pageName' => 'beta_page',
                'fragment' => 'beta',
            ]
        );

        // Settings
        $googlePlayStatus = Setting::get('google_play_status', 'moderation');
        $googlePlayUrl = Setting::get('google_play_url', 'https://play.google.com/store/apps/details?id=com.airsen.app');

        return view('admin.dashboard', compact(
            'totalVisits',
            'uniqueVisitors',
            'playStoreClicks',
            'simulatorRuns',
            'totalReviews',
            'approvedReviewsCount',
            'pendingReviewsCount',
            'betaSubscribersCount',
            'betaWaitlist',
            'chartLabels',
            'chartPageviews',
            'chartVisitors',
            'languageStats',
            'deviceStats',
            'recentVisits',
            'pendingReviews',
            'approvedReviews',
            'adminUsers',
            'googlePlayStatus',
            'googlePlayUrl'
        ));
    }

    /**
     * Handle Admin Login (Email + Password).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $credentials['is_active'] = true;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('admin.index');
        }

        return redirect()->route('admin.index')
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Daxil edilən e-poçt və ya şifrə yanlışdır!');
    }

    /**
     * Handle Admin Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.index');
    }

    /**
     * Approve a review.
     */
    public function approveReview(int $id): RedirectResponse
    {
        if (! Auth::user()?->isModerator()) {
            return redirect()->to(route('admin.index').'#reviews')->with('error', 'Rəyləri idarə etmək üçün Moderator hüququ tələb olunur!');
        }

        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);

        return redirect()->to(route('admin.index').'#reviews')
            ->with('success', '#'.$review->id.' nömrəli rəy uğurla təsdiqləndi və saytda dərc olundu.');
    }

    /**
     * Delete a review.
     */
    public function deleteReview(int $id): RedirectResponse
    {
        if (! Auth::user()?->isModerator()) {
            return redirect()->to(route('admin.index').'#reviews')->with('error', 'Rəyləri idarə etmək üçün Moderator hüququ tələb olunur!');
        }

        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->to(route('admin.index').'#reviews')
            ->with('success', '#'.$review->id.' nömrəli rəy sistemdən tam silindi.');
    }

    /**
     * Create a new Admin / Moderator / Analyst user.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#team')->with('error', 'Yeni admin əlavə etmək üçün Super Admin hüququ tələb olunur!');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
            'role' => ['required', 'in:super_admin,moderator,analyst'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->to(route('admin.index').'#team')
            ->with('success', 'Yeni istifadəçi ('.$validated['name'].') uğurla yaradıldı.');
    }

    /**
     * Update an existing Admin user.
     */
    public function updateUser(Request $request, int $id): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#team')->with('error', 'İstifadəçi məlumatlarını dəyişmək üçün Super Admin hüququ tələb olunur!');
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:super_admin,moderator,analyst'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:12'];
        }

        $validated = $request->validate($rules);

        if (
            $user->isSuperAdmin()
            && $validated['role'] !== 'super_admin'
            && User::where('role', 'super_admin')->where('is_active', true)->count() <= 1
        ) {
            return redirect()->to(route('admin.index').'#team')
                ->with('error', 'Sistemdə ən azı bir aktiv Super Admin qalmalıdır.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $user->update($data);

        return redirect()->to(route('admin.index').'#team')
            ->with('success', $user->name.' adlı istifadəçinin məlumatları uğurla yeniləndi.');
    }

    /**
     * Delete an Admin user.
     */
    public function deleteUser(int $id): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#team')->with('error', 'Bu əməliyyat üçün Super Admin hüququ tələb olunur!');
        }

        if (Auth::id() === $id) {
            return redirect()->to(route('admin.index').'#team')->with('error', 'Öz hesabınızı silə bilməzsiniz!');
        }

        $user = User::findOrFail($id);

        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->where('is_active', true)->count() <= 1) {
            return redirect()->to(route('admin.index').'#team')
                ->with('error', 'Son aktiv Super Admin hesabı silinə bilməz.');
        }

        $user->delete();

        return redirect()->to(route('admin.index').'#team')->with('success', $user->name.' adlı istifadəçi silindi.');
    }

    /**
     * Update user profile or password.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('admin.index');
        }

        $rules = [
            'current_password' => ['required', 'current_password'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:12'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $user->update($data);

        return redirect()->to(route('admin.index').'#settings')
            ->with('success', 'Profil məlumatlarınız uğurla yeniləndi.');
    }

    /**
     * Update Google Play settings.
     */
    public function updateGooglePlay(Request $request): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#settings')->with('error', 'Google Play parametrlərini yalnız Super Admin dəyişdirə bilər!');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:moderation,active'],
            'url' => ['required', 'url:http,https', 'max:2048'],
        ]);

        Setting::set('google_play_status', $validated['status']);
        Setting::set('google_play_url', $validated['url']);

        return redirect()->to(route('admin.index').'#settings')
            ->with('success', 'Google Play rejimi uğurla yeniləndi: '.strtoupper($validated['status']));
    }

    /**
     * Delete a single beta subscriber by email.
     */
    public function deleteBetaSubscriber(Request $request): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#beta')->with('error', 'Beta abunəçilərini yalnız Super Admin silə bilər!');
        }

        $validated = $request->validate(['email' => ['required', 'email', 'max:150']]);
        $email = Str::lower($validated['email']);
        $currentList = json_decode(Setting::get('beta_waitlist', '[]'), true) ?: [];
        $currentList = array_values(array_filter($currentList, fn ($item) => ($item['email'] ?? '') !== $email));
        Setting::set('beta_waitlist', json_encode($currentList));

        return redirect()->to(route('admin.index').'#beta')
            ->with('success', 'Abunəçi ('.$email.') uğurla silindi.');
    }

    /**
     * Clear all beta subscribers.
     */
    public function clearBetaSubscribers(): RedirectResponse
    {
        if (! Auth::user()?->isSuperAdmin()) {
            return redirect()->to(route('admin.index').'#beta')->with('error', 'Beta abunəçilərini yalnız Super Admin silə bilər!');
        }

        Setting::set('beta_waitlist', '[]');

        return redirect()->to(route('admin.index').'#beta')
            ->with('success', 'Bütün beta abunəçi siyahısı uğurla sıfırlandı.');
    }
}
