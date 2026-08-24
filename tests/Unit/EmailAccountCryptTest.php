<?php

namespace Tests\Unit;

use App\Models\EmailAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailAccountCryptTest extends TestCase
{
    use RefreshDatabase;

    public function test_decrypt_handles_legacy_plaintext(): void
    {
        // Insert raw plaintext without going through accessor
        \DB::table('email_accounts')->insert([
            'name' => 'Legacy',
            'email' => 'legacy@example.com',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_username' => 'user',
            'smtp_password' => 'plain-secret', // not encrypted
            'smtp_encryption' => 'tls',
            'from_name' => 'Legacy',
            'is_default' => false,
            'is_active' => true,
            'emails_sent' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $account = EmailAccount::where('email', 'legacy@example.com')->first();
        // Should not throw DecryptException, should return raw value
        $this->assertEquals('plain-secret', $account->smtp_password);
    }

    public function test_encrypted_password_round_trips(): void
    {
        $account = EmailAccount::create([
            'name' => 'Enc',
            'email' => 'enc@example.com',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_username' => 'user',
            'smtp_password' => 'my-secret',
            'smtp_encryption' => 'tls',
            'from_name' => 'Enc',
        ]);

        $fresh = EmailAccount::find($account->id);
        $this->assertEquals('my-secret', $fresh->smtp_password);
        // Stored value should not be plaintext
        $raw = \DB::table('email_accounts')->where('id', $account->id)->value('smtp_password');
        $this->assertNotEquals('my-secret', $raw);
    }
}
