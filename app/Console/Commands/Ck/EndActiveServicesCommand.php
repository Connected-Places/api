<?php

namespace App\Console\Commands\Ck;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class EndActiveServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:end-active-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks all service with a past end date as inactive';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {
        // Get all the services and chunk them for performance.
        Service::query()
            ->whereNotNull('ends_at')
            ->where('status', '=', Service::STATUS_ACTIVE)
            ->where('ends_at', '<', Date::now())
            ->chunk(200, function (Collection $services) {
                $services->each(function (Service $service) {
                    try {
                        $service->update(['status' => Service::STATUS_INACTIVE]);
                        $this->info("Service [{$service->id}] marked as inactive.");
                    } catch (\Exception $exception) {
                        $this->error("Failed to mark service [{$service->id}] as inactive.");
                    }
                });
            });
    }
}
