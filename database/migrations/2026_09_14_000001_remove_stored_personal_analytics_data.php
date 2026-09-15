<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('visits')->update(['ip_address' => null]);
        DB::table('reviews')->update(['ip_address' => null]);

        DB::table('visits')
            ->where('event_type', 'beta_subscribed')
            ->update(['device' => null]);

        $setting = DB::table('settings')->where('key', 'beta_waitlist')->first();

        if ($setting !== null) {
            $subscribers = json_decode((string) $setting->value, true);

            if (is_array($subscribers)) {
                $sanitizedSubscribers = array_map(function (array $subscriber): array {
                    unset($subscriber['ip']);

                    return $subscriber;
                }, $subscribers);

                DB::table('settings')->where('key', 'beta_waitlist')->update([
                    'value' => json_encode($sanitizedSubscribers, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Removed personal data cannot be reconstructed safely.
    }
};
