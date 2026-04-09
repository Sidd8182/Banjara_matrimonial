<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire active subscriptions that have reached their end date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredCount = UserSubscription::expireEnded();

        $this->info('Expired subscriptions updated: ' . $expiredCount);

        return self::SUCCESS;
    }
}
