<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get active admins who have permission to view orders.
        $admins = User::permission('orders.view')
            ->where('status', true)
            ->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No active admins found.');
            return;
        }

        $notifications = [
            [
                'type' => 'order',
                'title_ar' => 'طلب جديد',
                'title_en' => 'New Order',
                'body_ar' => 'تم استلام طلب جديد برقم #1001.',
                'body_en' => 'A new order #1001 has been received.',
                'url' => '/admin/orders',
            ],
            [
                'type' => 'order',
                'title_ar' => 'تم تأكيد الطلب',
                'title_en' => 'Order Confirmed',
                'body_ar' => 'تم تأكيد الطلب رقم #1002 بنجاح.',
                'body_en' => 'Order #1002 has been confirmed successfully.',
                'url' => '/admin/orders',
            ],
            [
                'type' => 'return',
                'title_ar' => 'طلب استرجاع جديد',
                'title_en' => 'New Return Request',
                'body_ar' => 'يوجد طلب استرجاع جديد يحتاج إلى المراجعة.',
                'body_en' => 'A new return request requires your review.',
                'url' => '/admin/returns',
            ],
            [
                'type' => 'customer',
                'title_ar' => 'عميل جديد',
                'title_en' => 'New Customer',
                'body_ar' => 'تم تسجيل عميل جديد في المتجر.',
                'body_en' => 'A new customer has registered in the store.',
                'url' => '/admin/customers',
            ],
            [
                'type' => 'system',
                'title_ar' => 'مرحباً بك في لوحة التحكم',
                'title_en' => 'Welcome to Dashboard',
                'body_ar' => 'يمكنك متابعة جميع تحديثات النظام من هنا.',
                'body_en' => 'You can follow all system updates from here.',
                'url' => '/admin/dashboard',
            ],
        ];

        foreach ($admins as $admin) {
            foreach ($notifications as $payload) {
                Notification::sendNow(
                    collect([$admin]),
                    new AppNotification($payload)
                );
            }
        }

        $this->command->info(
            count($notifications) . ' sample notifications created for '
            . $admins->count() . ' admins.'
        );
    }
}
