<?php

namespace Tests\Feature;

use App\Imports\ContactsImport;
use App\Models\EmailContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContactImportTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'testuser_'.uniqid().'@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_import_is_case_insensitive_duplicate(): void
    {
        $user = $this->makeUser();
        EmailContact::create([
            'email' => 'test@example.com',
            'user_id' => $user->id,
        ]);

        $import = new ContactsImport($user->id);
        $import->collection(new Collection([
            ['email' => 'TEST@EXAMPLE.COM', 'first_name' => 'Dup'],
            ['email' => 'new@example.com', 'first_name' => 'New'],
        ]));

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertEquals(1, $import->getSkippedCount());
        $this->assertEquals(2, EmailContact::where('user_id', $user->id)->count());
    }

    public function test_import_normalizes_email(): void
    {
        $user = $this->makeUser();
        $import = new ContactsImport($user->id);
        $import->collection(new Collection([
            ['email' => '  MixedCase@Example.COM  ', 'first_name' => 'A'],
        ]));

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertDatabaseHas('email_contacts', [
            'email' => 'mixedcase@example.com',
            'user_id' => $user->id,
        ]);
    }
}
