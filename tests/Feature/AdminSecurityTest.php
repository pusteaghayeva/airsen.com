<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_mutate_admin_resources(): void
    {
        $review = Review::create([
            'name' => 'Real visitor',
            'rating' => 5,
            'comment' => 'Useful project',
            'is_approved' => false,
            'locale' => 'en',
        ]);

        $this->post("/admin/reviews/{$review->id}/approve")->assertRedirect('/login');
        $this->post('/admin/beta/clear')->assertRedirect('/login');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);
    }

    public function test_web_responses_include_baseline_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_analyst_cannot_approve_a_review(): void
    {
        $analyst = User::factory()->create([
            'role' => 'analyst',
            'is_active' => true,
        ]);
        $review = Review::create([
            'name' => 'Real visitor',
            'rating' => 5,
            'comment' => 'Useful project',
            'is_approved' => false,
            'locale' => 'en',
        ]);

        $this->actingAs($analyst)
            ->post("/admin/reviews/{$review->id}/approve")
            ->assertRedirect(route('admin.index').'#reviews');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => 'secure-password',
            'role' => 'super_admin',
            'is_active' => false,
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertRedirect('/admin');

        $this->assertGuest();
    }

    public function test_google_play_settings_reject_invalid_values(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/settings/google-play', [
                'status' => 'invalid',
                'url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors(['status', 'url']);

        $this->assertNull(Setting::get('google_play_status'));
    }

    public function test_non_super_admins_do_not_receive_unrelated_dashboard_data(): void
    {
        $moderator = User::factory()->create([
            'role' => 'moderator',
            'is_active' => true,
        ]);
        User::factory()->create([
            'email' => 'private-admin@airsen.test',
            'role' => 'super_admin',
        ]);
        Setting::set('beta_waitlist', json_encode([
            ['email' => 'private-subscriber@example.com', 'locale' => 'en'],
        ]));
        Visit::create([
            'ip_hash' => hash('sha256', 'visitor'),
            'event_type' => 'page_view',
            'user_agent' => 'Private analytics marker',
        ]);

        $response = $this->actingAs($moderator)->get('/admin');

        $response->assertOk()
            ->assertDontSee('private-admin@airsen.test')
            ->assertDontSee('private-subscriber@example.com')
            ->assertDontSee('Private analytics marker');
    }

    public function test_profile_changes_require_the_current_password(): void
    {
        $admin = User::factory()->create([
            'password' => 'current-secure-password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post('/admin/profile', [
            'current_password' => 'wrong-password',
            'name' => 'Changed Name',
            'email' => $admin->email,
        ])->assertSessionHasErrors('current_password');

        $this->assertDatabaseMissing('users', [
            'id' => $admin->id,
            'name' => 'Changed Name',
        ]);
    }

    public function test_the_last_active_super_admin_cannot_be_demoted(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post("/admin/users/{$admin->id}/update", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'analyst',
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'super_admin',
        ]);
    }
}
