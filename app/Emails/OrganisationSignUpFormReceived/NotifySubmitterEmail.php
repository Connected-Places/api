<?php

namespace App\Emails\OrganisationSignUpFormReceived;

use App\Emails\Email;

class NotifySubmitterEmail extends Email
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('gov_uk_notify.notifications_template_ids.organisation_sign_up_form_received.notify_submitter.email');
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return 'emails.organisation.sign_up_form.received.notify_submitter.content';
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return 'emails.organisation.sign_up_form.received.notify_submitter.subject';
    }
}
