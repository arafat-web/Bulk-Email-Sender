<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactTag;
use App\Models\EmailContact;
use App\Models\User;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the first user (admin)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        // Create sample tags
        $tags = [
            [
                'name' => 'VIP Customers',
                'color' => '#dc3545',
                'description' => 'High-value customers requiring special attention',
                'user_id' => $user->id
            ],
            [
                'name' => 'Newsletter Subscribers',
                'color' => '#0d6efd',
                'description' => 'Users subscribed to our newsletter',
                'user_id' => $user->id
            ],
            [
                'name' => 'Prospects',
                'color' => '#ffc107',
                'description' => 'Potential customers we are following up with',
                'user_id' => $user->id
            ],
            [
                'name' => 'Partners',
                'color' => '#198754',
                'description' => 'Business partners and affiliates',
                'user_id' => $user->id
            ],
            [
                'name' => 'Support Contacts',
                'color' => '#6610f2',
                'description' => 'Technical support and customer service contacts',
                'user_id' => $user->id
            ]
        ];

        foreach ($tags as $tagData) {
            ContactTag::firstOrCreate(
                ['name' => $tagData['name'], 'user_id' => $user->id],
                $tagData
            );
        }

        // Create sample contacts
        $contacts = [
            [
                'email' => 'john.doe@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'company' => 'ABC Corporation',
                'phone' => '+1 (555) 123-4567',
                'notes' => 'Lead developer at ABC Corp. Interested in our enterprise solutions.',
                'status' => 'active',
                'tags' => ['VIP Customers', 'Newsletter Subscribers']
            ],
            [
                'email' => 'jane.smith@techstartup.com',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'company' => 'Tech Startup Inc',
                'phone' => '+1 (555) 987-6543',
                'notes' => 'CTO of growing startup. Looking for scalable email solutions.',
                'status' => 'active',
                'tags' => ['Prospects', 'Newsletter Subscribers']
            ],
            [
                'email' => 'mike.wilson@globalcorp.com',
                'first_name' => 'Mike',
                'last_name' => 'Wilson',
                'company' => 'Global Corp',
                'phone' => '+1 (555) 456-7890',
                'notes' => 'Marketing director. Runs large email campaigns.',
                'status' => 'active',
                'tags' => ['VIP Customers', 'Partners']
            ],
            [
                'email' => 'sarah.johnson@consulting.com',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'company' => 'Johnson Consulting',
                'phone' => '+1 (555) 321-9876',
                'notes' => 'Independent consultant specializing in email marketing.',
                'status' => 'active',
                'tags' => ['Partners', 'Newsletter Subscribers']
            ],
            [
                'email' => 'support@helpdesk.com',
                'first_name' => null,
                'last_name' => null,
                'company' => 'HelpDesk Solutions',
                'phone' => '+1 (555) 111-2222',
                'notes' => 'Support team contact for integration issues.',
                'status' => 'active',
                'tags' => ['Support Contacts']
            ]
        ];

        foreach ($contacts as $contactData) {
            $tagNames = $contactData['tags'];
            unset($contactData['tags']);

            $contact = EmailContact::firstOrCreate(
                ['email' => $contactData['email'], 'user_id' => $user->id],
                array_merge($contactData, ['user_id' => $user->id])
            );

            // Attach tags
            if (!empty($tagNames)) {
                $tagIds = ContactTag::where('user_id', $user->id)
                    ->whereIn('name', $tagNames)
                    ->pluck('id')
                    ->toArray();

                $contact->tags()->sync($tagIds);
            }
        }

        $this->command->info('Sample contacts and tags created successfully!');
    }
}
