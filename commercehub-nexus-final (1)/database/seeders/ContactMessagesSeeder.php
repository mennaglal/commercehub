<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContactMessagesSeeder extends Seeder
{
    public function run()
    {
        if (ContactMessage::exists()) {
            return;
        }

        $messages = [
            [
                'name' => 'Ahmed Mohamed',
                'email' => 'ahmed@example.test',
                'phone' => '01012345678',
                'subject' => 'Order delivery inquiry',
                'message' => 'I would like to know when my order will be delivered.',
                'status' => ContactMessage::STATUS_NEW,
            ],
            [
                'name' => 'Sara Ali',
                'email' => 'sara@example.test',
                'phone' => '01098765432',
                'subject' => 'Product complaint',
                'message' => 'The product I received does not match the description.',
                'status' => ContactMessage::STATUS_READ,
            ],
            [
                'name' => 'Mohamed Hassan',
                'email' => 'mohamed@example.test',
                'phone' => '01112345678',
                'subject' => 'Payment problem',
                'message' => 'My payment was completed but the order still shows pending.',
                'status' => ContactMessage::STATUS_REPLIED,
            ],
            [
                'name' => 'Mona Ibrahim',
                'email' => 'mona@example.test',
                'phone' => '01212345678',
                'subject' => 'Return request',
                'message' => 'I need help with returning one of the items in my order.',
                'status' => ContactMessage::STATUS_NEW,
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar@example.test',
                'phone' => '01512345678',
                'subject' => 'Vendor registration',
                'message' => 'I want to register as a vendor on your platform.',
                'status' => ContactMessage::STATUS_CLOSED,
            ],
            [
                'name' => 'Nour Samir',
                'email' => 'nour@example.test',
                'phone' => '01055556666',
                'subject' => 'Account issue',
                'message' => 'I cannot update my account information.',
                'status' => ContactMessage::STATUS_NEW,
            ],
        ];

        foreach ($messages as $index => $message) {
            $createdAt = Carbon::now()->subDays(($index * 2) + 1);

            $message['created_at'] = $createdAt;
            $message['updated_at'] = $createdAt;

            if ($message['status'] !== ContactMessage::STATUS_NEW) {
                $message['read_at'] = $createdAt->copy()->addHours(2);
            }

            if ($message['status'] === ContactMessage::STATUS_REPLIED) {
                $message['replied_at'] = $createdAt->copy()->addHours(5);
            }

            ContactMessage::create($message);
        }
    }
}
