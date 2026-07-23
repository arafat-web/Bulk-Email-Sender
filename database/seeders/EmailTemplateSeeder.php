<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Our Platform!',
                'body' => '<h1>Welcome!</h1>
<p>Dear Valued Customer,</p>
<p>Thank you for joining our platform. We are excited to have you on board!</p>
<p>If you have any questions, feel free to reach out to our support team.</p>
<p>Best regards,<br>The Team</p>',
                'description' => 'A warm welcome email for new subscribers',
                'is_active' => true,
            ],
            [
                'name' => 'Newsletter Template',
                'subject' => 'Monthly Newsletter - {{month}}',
                'body' => '<h2>Monthly Newsletter</h2>
<p>Hello,</p>
<p>Here are the latest updates from our team this month:</p>
<ul>
    <li>Update 1: New features released</li>
    <li>Update 2: Upcoming events</li>
    <li>Update 3: Community highlights</li>
</ul>
<p>Thank you for being a part of our community!</p>
<p>Best,<br>Your Team</p>',
                'description' => 'Monthly newsletter template',
                'is_active' => true,
            ],
            [
                'name' => 'Product Announcement',
                'subject' => 'Exciting New Product Launch!',
                'body' => '<h1>New Product Alert!</h1>
<p>We are thrilled to announce the launch of our latest product!</p>
<p><strong>Key Features:</strong></p>
<ul>
    <li>Feature 1</li>
    <li>Feature 2</li>
    <li>Feature 3</li>
</ul>
<p>Learn more and get started today!</p>
<p>Cheers,<br>The Product Team</p>',
                'description' => 'Template for announcing new products or services',
                'is_active' => true,
            ],
            [
                'name' => 'Event Invitation',
                'subject' => 'You\'re Invited to Our Exclusive Event',
                'body' => '<h2>You\'re Invited!</h2>
<p>Dear Guest,</p>
<p>We would love for you to join us at our upcoming event.</p>
<p><strong>Event Details:</strong></p>
<ul>
    <li>Date: [Event Date]</li>
    <li>Time: [Event Time]</li>
    <li>Location: [Event Location]</li>
</ul>
<p>Please RSVP by [RSVP Date].</p>
<p>We look forward to seeing you there!</p>
<p>Warm regards,<br>Event Team</p>',
                'description' => 'Invitation template for events',
                'is_active' => true,
            ],
            [
                'name' => 'Promotional Offer',
                'subject' => 'Special Offer Just for You - Save 20%!',
                'body' => '<h1>Exclusive Offer!</h1>
<p>Hi there,</p>
<p>As a valued customer, we\'re offering you an exclusive <strong>20% discount</strong> on all products!</p>
<p>Use code: <strong>SAVE20</strong> at checkout.</p>
<p>Offer valid until [Expiry Date].</p>
<p>Shop now and save big!</p>
<p>Happy Shopping,<br>Sales Team</p>',
                'description' => 'Template for promotional offers and discounts',
                'is_active' => true,
            ],
            [
                'name' => 'Thank You Email',
                'subject' => 'Thank You for Your Purchase!',
                'body' => '<h2>Thank You!</h2>
<p>Dear Customer,</p>
<p>Thank you for your recent purchase. We truly appreciate your business!</p>
<p>Your order details:</p>
<ul>
    <li>Order Number: [Order Number]</li>
    <li>Total Amount: [Amount]</li>
</ul>
<p>If you have any questions about your order, please don\'t hesitate to contact us.</p>
<p>Thank you again for choosing us!</p>
<p>Best regards,<br>Customer Service Team</p>',
                'description' => 'Thank you email after purchase',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
