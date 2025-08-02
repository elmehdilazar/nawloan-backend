<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\LocalNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CheckOutstandingPayments extends Command
{
    protected $signature = 'payments:check';
    protected $description = 'Check outstanding balances, send reminders, and freeze accounts if unpaid';

    public function handle()
    {
        $today = now();
        $dueDate = Carbon::now()->startOfMonth()->addDays(10);

        // Get all companies & factories that have outstanding balances
        $users = User::whereHas('userData', function ($query) {
            $query->where('outstanding_balance', '>', 0);
        })->get();

        foreach ($users as $user) {
            // Send payment reminders from the 1st to the 10th
            if ($today->lessThanOrEqualTo($dueDate)) {
                $this->sendReminder($user);
            }

            // Freeze accounts after the 10th if payment is not made
            if ($today->greaterThan($dueDate)) {
                $this->freezeAccount($user);
            }
        }

        $this->info('Payment checks completed successfully.');
    }

    private function sendReminder($user)
    {
        $notificationData = [
            'title' => 'Payment Reminder',
            'body' => 'Your outstanding balance is ' . number_format($user->userData->outstanding_balance, 2) . ' EGP. Please pay before the 10th to avoid account freezing.',
            'target' => 'payment',
            'target_id' => null,
            'link'  => route('admin.transactions.index'),
            'sender' => 'Nawloan System',
        ];

        Notification::send($user, new LocalNotification($notificationData));
        $this->info("Reminder sent to User ID: {$user->id}");
    }

    private function freezeAccount($user)
    {
        // Freeze the account
        $user->update(['active' => false]);

        $notificationData = [
            'title' => 'Account Frozen',
            'body' => 'Your account has been frozen due to unpaid outstanding balance. Please pay your dues to reactivate your account.',
            'target' => 'account',
            'target_id' => null,
            'link'  => route('admin.transactions.index'),
            'sender' => 'Nawloan System',
        ];

        Notification::send($user, new LocalNotification($notificationData));
        $this->info("Account frozen for User ID: {$user->id}");
    }
}
?>
