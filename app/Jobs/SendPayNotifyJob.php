<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserData;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;

class SendPayNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;
    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
          //  Log::info('$this->data'.' '.$this->data);
        $message =Lang::get('site.date_of_pay_notice_msg') ;
        $message2 =Lang::get('site.date_of_pay_notice_msg1') ;
        $title = Lang::get('site.date_of_pay_notice_title');
        $message1= Lang::get('site.disable_account_notice_msg');
        $title1 = Lang::get('site.disable_account_notice_title');
        $admin=User::find(1);
        foreach($this->data as $company){
            $date3=date('d');
            $comp=UserData::where('user_id',$company->id)->where('type','factory')->get()->first();
            if(!empty($comp->date_of_payment) && $date3 <= $comp->date_of_payment - 3 && $comp->outstanding_balance > 0) {
            $date2=date('Y-m-').$comp->date_of_payment;
                Log::info($title);
                Log::info($message);
                Log::info($message2);
                Log::info($title1);
                Log::info($message1);
//                Notification::send($admin, new FcmPushNotification($title, $message.' '.$date2 .' '.$message2, [$admin->fcm_token]));
            if (!empty($company->fcm_token)) {
                Log::info($company->fcm_token);
                Notification::send($company, new FcmPushNotification($title, $message.' '.$date2 .' '.$message2, [$company->fcm_token]));
            }
            }/**/
            if (!empty($comp->date_of_payment) && $date3 > $comp->date_of_payment + 1 && $comp->outstanding_balance > 0) {
            $date2=date('Y-m-').$comp->date_of_payment;
                //Notification::send($admin, new FcmPushNotification($title1, $message1.' '.$date2 , [$admin->fcm_token]));
                    $company->update(['active'=>0]);
                    $comp->update(['status'=>'disabled']);
                    Log::info('Disable customer company account : id = '.$company->id . ', name : '.$company->name .' , phone : '.$company->phone);
                Log::info($title);
                Log::info($message);
                Log::info($message2);
                Log::info($title1);
                Log::info($message1);
                if (!empty($company->fcm_token) && $company->active==1) {
                    Log::info($company->fcm_token);
                    Notification::send($company, new FcmPushNotification($title1, $message1.' '.$date2 , [$company->fcm_token]));
                }
            }

        }
    }

}
