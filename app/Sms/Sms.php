<?php

namespace App\Sms;

use App\Contracts\SmsSender;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

abstract class Sms implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $to;

    /**
     * @var array
     */
    public $values;

    /**
     * @var string
     */
    public $templateId;

    /**
     * @var string|null
     */
    public $reference;

    /**
     * @var string|null
     */
    public $senderId;

    /**
     * @var \App\Models\Notification|null
     */
    public $notification;

    /**
     * Sms constructor.
     */
    public function __construct(string $to, array $values = [])
    {
        $this->queue = config('queue.queues.notifications', 'default');

        $this->to = $to;
        $this->values = $values;
        $this->templateId = $this->getTemplateId();
        $this->reference = $this->getReference();
        $this->senderId = $this->getSenderId();
    }

    protected function getTemplateId(): ?string
    {
        return null;
    }

    protected function getReference(): ?string
    {
        return null;
    }

    protected function getSenderId(): ?string
    {
        return null;
    }

    abstract public function getContent(): string;

    public function send()
    {
        $this->handle(resolve(SmsSender::class));
    }

    /**
     * Execute the job.
     */
    public function handle(SmsSender $smsSender)
    {
        try {
            // Send the SMS.
            $smsSender->send($this);

            // Update the notification.
            if ($this->notification) {
                $this->notification->update(['sent_at' => Date::now()]);
            }
        } catch (Exception $exception) {
            // Log the error.
            logger()->error($exception);

            // Update the notification.
            if ($this->notification) {
                $this->notification->update(['failed_at' => Date::now()]);
            }
        }
    }
}
