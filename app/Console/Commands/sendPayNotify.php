<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendPayNotifyJob;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Log;

class sendPayNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment time notification to shipping companies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies=User::where('type', 'factory')->chunk(50,function($data){
            dispatch(new SendPayNotifyJob($data));
        });
        //Log::info($companies);
        return 0;
    }
}
