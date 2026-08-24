<?php

namespace Tests\Feature;

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailAccountTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser_'.uniqid().'@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_guest_cannot_access_email_accounts(): void
    {
        $this->get(route('email-accounts.index'))->assertRedirect(route('login'));
    }

    public function test_store_rejects_mass_assignment_of_is_default(): void
    {
        $this->actingUser();

        $this->post(route('email-accounts.store'), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_username' => 'user',
            'smtp_password' => 'secret123',
            'smtp_encryption' => 'tls',
            'from_name' => 'Tester',
            'notes' => '',
            // attempt mass-assignment
            'is_default' => true,
            'is_active' => true,
            'emails_sent' => 9999,
        ])->assertRedirect(route('email-accounts.index'));

        // First account is auto-default, but emails_sent must not be 9999
        $account = EmailAccount::where('email', 'test@example.com')->first();
        $this->assertNotNull($account);
        $this->assertEquals(0, $account->emails_sent, 'emails_sent must not be mass-assignable');
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingUser();
        $this->post(route('email-accounts.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_name']);
    }
}
