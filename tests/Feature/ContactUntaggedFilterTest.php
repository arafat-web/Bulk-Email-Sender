<?php

namespace Tests\Feature;

use App\Models\ContactTag;
use App\Models\EmailContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContactUntaggedFilterTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'Marketer',
            'email' => 'marketer_'.uniqid().'@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_can_filter_select_export_and_bulk_tag_untagged_contacts(): void
    {
        $user = $this->actingUser();

        $brazil = ContactTag::create(['name' => 'Brazilian', 'color' => '#22c55e', 'user_id' => $user->id]);
        $indonesia = ContactTag::create(['name' => 'Indonesia', 'color' => '#ef4444', 'user_id' => $user->id]);

        $brContact = EmailContact::create(['email' => 'br@example.com', 'user_id' => $user->id]);
        $brContact->tags()->attach($brazil->id);

        $idContact1 = EmailContact::create(['email' => 'id1@example.com', 'user_id' => $user->id]);
        $idContact2 = EmailContact::create(['email' => 'id2@example.com', 'user_id' => $user->id]);

        // 1. Index filter shows only untagged contacts
        $this->get(route('contacts.index', ['tag' => 'untagged']))
            ->assertOk()
            ->assertSee('id1@example.com')
            ->assertSee('id2@example.com')
            ->assertDontSee('br@example.com');

        // 2. Select-all-filtered IDs endpoint returns only untagged IDs
        $this->getJson(route('contacts.ids', ['tag' => 'untagged']))
            ->assertOk()
            ->assertJson([
                'count' => 2,
                'total' => 2,
                'capped' => false,
            ]);

        // 3. Export CSV with tag=untagged succeeds and includes only untagged contacts
        $export = $this->get(route('contacts.export', ['tag' => 'untagged']));
        $export->assertOk();
        $csv = $export->streamedContent();
        $this->assertStringContainsString('id1@example.com', $csv);
        $this->assertStringContainsString('id2@example.com', $csv);
        $this->assertStringNotContainsString('br@example.com', $csv);

        // 4. Bulk Add Tag to all filtered untagged contacts assigns Indonesia only to them
        $this->post(route('contacts.bulk-action'), [
            'action' => 'add_tag',
            'tag_id' => $indonesia->id,
            'bulk_all_filtered' => '1',
            'bulk_tag' => 'untagged',
        ])->assertRedirect();

        $this->assertTrue($idContact1->fresh()->tags->contains($indonesia->id));
        $this->assertTrue($idContact2->fresh()->tags->contains($indonesia->id));
        $this->assertFalse($brContact->fresh()->tags->contains($indonesia->id));
        $this->assertTrue($brContact->fresh()->tags->contains($brazil->id));
    }
}
