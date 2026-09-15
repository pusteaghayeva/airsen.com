<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('AirSen');
        $response->assertSee('28 900');
        $response->assertSee('Safarli');
    }

    public function test_multilingual_routing_and_translations(): void
    {
        // Azerbaijani
        $responseAz = $this->get('/az');
        $responseAz->assertStatus(200);
        $responseAz->assertSee('Dəm Qazından Ağıllı Mühafizə');

        // Russian
        $responseRu = $this->get('/ru');
        $responseRu->assertStatus(200);
        $responseRu->assertSee('Умная защита от угарного газа');

        // English
        $responseEn = $this->get('/en');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Smart Carbon Monoxide Protection');
    }

    public function test_analytics_event_tracking(): void
    {
        $payload = [
            'event_type' => 'google_play_click',
            'language' => 'az',
            'page_url' => '/',
            'country' => 'Azerbaijan',
        ];

        $response = $this->postJson('/api/track', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'recorded']);

        $this->assertDatabaseHas('visits', [
            'event_type' => 'google_play_click',
            'language' => 'az',
        ]);
    }

    public function test_review_submission_with_mandatory_premoderation(): void
    {
        $payload = [
            'name' => 'Kamil Əhmədov',
            'rating' => 5,
            'comment' => 'Əla ideyadır və çox lazımlı təhlükəsizlik sistemidir!',
            'locale' => 'az',
        ];

        $response = $this->postJson('/api/reviews/submit', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify premoderation requirement (is_approved: false)
        $this->assertDatabaseHas('reviews', [
            'name' => 'Kamil Əhmədov',
            'rating' => 5,
            'is_approved' => false,
        ]);

        // Ensure unapproved review is NOT shown on the public landing page
        $landingResponse = $this->get('/');
        $landingResponse->assertDontSee('Kamil Əhmədov');
    }

    public function test_loading_landing_page_never_deletes_existing_reviews(): void
    {
        $review = Review::create([
            'name' => 'Real Visitor',
            'rating' => 5,
            'comment' => 'This review must remain stored.',
            'is_approved' => true,
            'locale' => 'en',
        ]);

        $this->get('/en')->assertOk()->assertSee('Real Visitor');

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_admin_login_and_review_approval(): void
    {
        $review = Review::create([
            'name' => 'Rəşad Vəliyev',
            'rating' => 5,
            'comment' => 'Ev üçün çox faydalı tətbiqdir!',
            'is_approved' => false,
            'locale' => 'az',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@airsen.test',
            'password' => Hash::make('secure-password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $badLogin = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);
        $badLogin->assertRedirect('/admin');
        $this->assertGuest();

        $goodLogin = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'secure-password',
        ]);
        $goodLogin->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);

        $approveResponse = $this->post("/admin/reviews/{$review->id}/approve");

        $approveResponse->assertRedirect(route('admin.index').'#reviews');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);

        // Now it must appear on the public landing page
        $landingResponse = $this->get('/');
        $landingResponse->assertSee('Rəşad Vəliyev');
    }

    public function test_google_play_setting_toggle(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->post('/admin/settings/google-play', [
                'status' => 'active',
                'url' => 'https://play.google.com/store/apps/details?id=com.airsen.live',
            ]);

        $this->assertEquals('active', Setting::get('google_play_status'));
        $this->assertEquals('https://play.google.com/store/apps/details?id=com.airsen.live', Setting::get('google_play_url'));
    }

    public function test_user_update_by_super_admin(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@airsen.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $moderator = User::create([
            'name' => 'Old Moderator',
            'email' => 'mod@airsen.com',
            'password' => Hash::make('password123'),
            'role' => 'moderator',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->post("/admin/users/{$moderator->id}/update", [
                'name' => 'Updated Moderator Name',
                'email' => 'newmod@airsen.com',
                'role' => 'analyst',
            ]);

        $response->assertRedirect(route('admin.index').'#team');

        $this->assertDatabaseHas('users', [
            'id' => $moderator->id,
            'name' => 'Updated Moderator Name',
            'email' => 'newmod@airsen.com',
            'role' => 'analyst',
        ]);
    }
}
