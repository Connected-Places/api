<?php

namespace App\Emails\ReferralIncompleted;

use App\Emails\Email;

class NotifyRefereeEmail extends Email
{
    /**
     * @return string
     */
    protected function getTemplateId(): string
    {
        return config('tlr.notifications_template_ids.referral_incompleted.notify_referee.email');
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return <<<'EOT'
Hi ((REFEREE_NAME)),

The referral you made to ((SERVICE_NAME)) has been marked as incomplete with the following message:

“((REFERRAL_STATUS))“.

If you believe the service did not try to contact the client, or you have any other feedback regarding the connection, please contact us at info@looprepository.org.

Many thanks,

The LOOP team.
EOT;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return 'Referral Incompleted';
    }
}
