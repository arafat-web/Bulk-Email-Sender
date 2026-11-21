<?php

namespace Database\Seeders;

use App\Models\EmailKeyword;
use Illuminate\Database\Seeder;

class EmailKeywordSeeder extends Seeder
{
    /**
     * Seed the built-in keywords matching the existing contact/CSV fields.
     */
    public function run(): void
    {
        $defaults = [
            ['key' => 'name', 'label' => 'Full Name', 'source_column' => 'full_name', 'default_value' => 'there', 'description' => 'Recipient full name (falls back to first name).'],
            ['key' => 'first_name', 'label' => 'First Name', 'source_column' => 'first_name', 'default_value' => '', 'description' => 'Recipient first / given name.'],
            ['key' => 'last_name', 'label' => 'Last Name', 'source_column' => 'last_name', 'default_value' => '', 'description' => 'Recipient last / family name.'],
            ['key' => 'email', 'label' => 'Email Address', 'source_column' => 'email', 'default_value' => '', 'description' => 'Recipient email address.'],
            ['key' => 'phone', 'label' => 'Phone', 'source_column' => 'phone', 'default_value' => '', 'description' => 'Recipient phone number.'],
            ['key' => 'company_name', 'label' => 'Company Name', 'source_column' => 'company', 'default_value' => '', 'description' => 'Recipient company (alias of company).'],
            ['key' => 'company', 'label' => 'Company', 'source_column' => 'company', 'default_value' => '', 'description' => 'Recipient company.'],
            ['key' => 'notes', 'label' => 'Notes', 'source_column' => 'notes', 'default_value' => '', 'description' => 'Extra notes field.'],
        ];

        foreach ($defaults as $row) {
            EmailKeyword::updateOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }

        EmailKeyword::flushCache();
    }
}
