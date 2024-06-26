<?php

namespace Tests\Unit\Console\Commands\Ck\Notify;

use App\Console\Commands\Ck\Notify\UnactionedReferralsCommand;
use App\Emails\ReferralUnactioned\NotifyServiceEmail;
use App\Models\Referral;
use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UnactionedReferralsCommandTest extends TestCase
{
    public function test_emails_sent(): void
    {
        Queue::fake();

        $service = Service::factory()->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail(),
        ]);

        Referral::factory()->create([
            'service_id' => $service->id,
            'email' => 'test@example.com',
            'referee_email' => 'test@example.com',
            'status' => Referral::STATUS_NEW,
            'created_at' => Date::now()->subWeekdays(6),
        ]);

        Artisan::call(UnactionedReferralsCommand::class);

        Queue::assertPushedOn(config('queue.queues.notifications', 'default'), NotifyServiceEmail::class);
        Queue::assertPushed(NotifyServiceEmail::class, function (NotifyServiceEmail $email) {
            $this->assertArrayHasKey('REFERRAL_SERVICE_NAME', $email->values);
            $this->assertArrayHasKey('REFERRAL_INITIALS', $email->values);
            $this->assertArrayHasKey('REFERRAL_ID', $email->values);
            $this->assertArrayHasKey('REFERRAL_DAYS_AGO', $email->values);
            $this->assertArrayHasKey('REFERRAL_TYPE', $email->values);
            $this->assertArrayHasKey('REFERRAL_CONTACT_METHOD', $email->values);
            $this->assertArrayHasKey('REFERRAL_DAYS_LEFT', $email->values);

            return true;
        });
    }
}
