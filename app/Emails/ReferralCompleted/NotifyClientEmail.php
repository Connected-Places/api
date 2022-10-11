<?php

namespace App\Emails\ReferralCompleted;

use App\Emails\Email;

class NotifyClientEmail extends Email
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('gov_uk_notify.notifications_template_ids.referral_completed.notify_client.email');
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return 'emails.referral.completed.notify_client.content';
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return 'emails.referral.completed.notify_client.subject';
    }
}
