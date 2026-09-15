<?php

namespace Tests\Feature;

use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_rejects_unknown_events_and_external_urls(): void
    {
        $this->postJson('/api/track', [
            'event_type' => 'forged_event',
            'language' => 'xx',
            'page_url' => 'https://attacker.example',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['event_type', 'language', 'page_url']);

        $this->assertDatabaseCount('visits', 0);
    }

    public function test_analytics_does_not_store_raw_ip_or_client_country(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->postJson('/api/track', [
                'event_type' => 'page_view',
                'language' => 'az',
                'page_url' => '/',
                'country' => 'Forged Country',
            ])->assertOk();

        $visit = Visit::query()->firstOrFail();

        $this->assertSame('page_view', $visit->event_type);
        $this->assertSame('Direct / Unknown', $visit->country);
        $this->assertArrayNotHasKey('ip_address', $visit->getAttributes());
    }

    public function test_review_rating_must_be_an_integer_between_one_and_five(): void
    {
        $this->postJson('/api/reviews/submit', [
            'name' => 'Visitor',
            'rating' => 0.5,
            'comment' => 'Feedback',
            'locale' => 'en',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['rating']);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_tracking_stores_only_the_referer_path(): void
    {
        $this->withHeader('Referer', 'https://airsen.com/en?token=sensitive#reviews')
            ->postJson('/api/reviews/submit', [
                'name' => 'Visitor',
                'rating' => 5,
                'comment' => 'Feedback',
                'locale' => 'en',
            ])->assertOk();

        $this->assertDatabaseHas('visits', [
            'event_type' => 'review_submitted',
            'page_url' => '/en',
        ]);
    }
}
