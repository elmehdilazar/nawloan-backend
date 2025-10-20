<?php

namespace App\Jobs;

use App\Models\Coupon;
use App\Models\User;
use App\Notifications\FcmPushNotification;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendCouponNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $titleKey;
    protected int $couponId;
    protected string $sender;

    /**
     * Create a new job instance.
     */
    public function __construct(string $titleKey, int $couponId, string $sender)
    {
        $this->titleKey = $titleKey;
        $this->couponId = $couponId;
        $this->sender = $sender;
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $coupon = Coupon::withTrashed()->find($this->couponId);
        if (!$coupon) {
            return;
        }

        $couponName = $coupon->name;
        $link = route('admin.coupons.index', ['name' => $couponName]);

        [$titleEn, $titleAr, $bodyEn, $bodyAr] = $this->buildLocalizedContent($coupon, $this->titleKey);

        $dataAdmin = [
            'title'     => $this->titleKey,
            'body'      => ['en' => $bodyEn, 'ar' => $bodyAr],
            'target'    => 'coupon',
            'object'    => $this->mapCouponObject($coupon),
            'link'      => $link,
            'target_id' => $couponName,
            'sender'    => $this->sender,
        ];

        $dataFront = [
            'title'     => ['en' => $titleEn, 'ar' => $titleAr],
            'body'      => ['en' => $bodyEn, 'ar' => $bodyAr],
            'target'    => 'coupon',
            'object'    => $this->mapCouponObject($coupon),
            'link'      => $link,
            'target_id' => $couponName,
            'sender'    => $this->sender,
        ];

        $fcmTitle = ['en' => $titleEn, 'ar' => $titleAr];
        $fcmBody  = ['en' => $bodyEn,  'ar' => $bodyAr];
        $fcmData  = ['target' => 'coupon', 'target_id' => (string) $couponName];

        // Notify admins
        User::whereIn('type', ['admin', 'superadministrator'])
            ->chunkById(200, function ($admins) use ($dataAdmin, $fcmTitle, $fcmBody, $fcmData) {
                Notification::send($admins, new LocalNotification($dataAdmin));
                foreach ($admins as $admin) {
                    if (!empty($admin->fcm_token)) {
                        Notification::send($admin, new FcmPushNotification($fcmTitle, $fcmBody, [$admin->fcm_token], $fcmData));
                    }
                }
            });

        // Notify providers
        User::where('user_type', 'service_provider')
            ->chunkById(200, function ($providers) use ($dataFront, $fcmTitle, $fcmBody, $fcmData) {
                Notification::send($providers, new LocalNotification($dataFront));
                foreach ($providers as $provider) {
                    if (!empty($provider->fcm_token)) {
                        Notification::send($provider, new FcmPushNotification($fcmTitle, $fcmBody, [$provider->fcm_token], $fcmData));
                    }
                }
            });

        // Notify seekers (users/factories)
        User::whereIn('type', ['user', 'factory'])
            ->chunkById(200, function ($seekers) use ($dataFront, $fcmTitle, $fcmBody, $fcmData) {
                Notification::send($seekers, new LocalNotification($dataFront));
                foreach ($seekers as $seeker) {
                    if (!empty($seeker->fcm_token)) {
                        Notification::send($seeker, new FcmPushNotification($fcmTitle, $fcmBody, [$seeker->fcm_token], $fcmData));
                    }
                }
            });
    }

    private function buildLocalizedContent(Coupon $coupon, string $titleKey): array
    {
        $durationEn = '';
        $durationAr = '';

        try {
            if (!empty($coupon->start_date) && !empty($coupon->expiry_date)) {
                $start = Carbon::parse($coupon->start_date);
                $end   = Carbon::parse($coupon->expiry_date);
                $durationDays = max(1, $start->diffInDays($end));

                if ($durationDays === 1) {
                    $durationEn = 'for one day';
                    $durationAr = 'لمدة يوم واحد';
                } elseif ($durationDays === 7) {
                    $durationEn = 'for one week';
                    $durationAr = 'لمدة أسبوع واحد';
                } elseif ($durationDays >= 28 && $durationDays <= 31) {
                    $durationEn = 'for one month';
                    $durationAr = 'لمدة شهر واحد';
                } else {
                    $durationEn = "for {$durationDays} days";
                    $durationAr = "لمدة {$durationDays} يوم";
                }
            }
        } catch (\Throwable $e) {
            $durationEn = $durationAr = '';
        }

        $discountValue = (string) ($coupon->discount ?? '');
        $isPercent = strtolower($coupon->type ?? '') === 'percentage';
        $discountEn = $isPercent ? ($discountValue . '% discount') : ($discountValue . ' discount');
        $discountAr = $isPercent ? ('خصم ' . $discountValue . '%') : ('خصم بقيمة ' . $discountValue);

        $titleEn = match ($titleKey) {
            'add'     => 'New coupon added',
            'edit'    => 'Coupon updated',
            'enable'  => 'Coupon enabled',
            'disable' => 'Coupon disabled',
            default   => 'Coupon update',
        };

        $titleAr = match ($titleKey) {
            'add'     => 'تم إضافة كوبون جديد',
            'edit'    => 'تم تحديث الكوبون',
            'enable'  => 'تم تفعيل الكوبون',
            'disable' => 'تم تعطيل الكوبون',
            default   => 'تحديث على الكوبون',
        };

        $bodyEnParts = array_filter([$discountEn, $durationEn]);
        $bodyArParts = array_filter([$discountAr, $durationAr]);

        $bodyEn = trim(implode(' ', $bodyEnParts));
        $bodyAr = trim(implode(' ', $bodyArParts));

        return [$titleEn, $titleAr, $bodyEn, $bodyAr];
    }

    private function mapCouponObject(Coupon $coupon): array
    {
        return [
            'name'        => $coupon->name,
            'code'        => $coupon->code,
            'discount'    => $coupon->discount,
            'type'        => $coupon->type,
            'start_date'  => $coupon->start_date,
            'expiry_date' => $coupon->expiry_date,
        ];
    }
}
