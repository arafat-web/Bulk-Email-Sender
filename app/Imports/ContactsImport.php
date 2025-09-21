<?php

namespace App\Imports;

use App\Models\EmailContact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContactsImport implements ToCollection, WithHeadingRow
{
    private $userId;
    private $tags;
    private $importedCount = 0;
    private $skippedCount = 0;

    public function __construct($userId, $tags = [])
    {
        $this->userId = $userId;
        $this->tags = $tags;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // Map new column names to database fields
                $emailField = $row['email_address'] ?? $row['email'] ?? null;
                $firstNameField = $row['given_name'] ?? $row['first_name'] ?? null;
                $lastNameField = $row['family_name'] ?? $row['last_name'] ?? null;
                $phoneField = $row['phone'] ?? null;
                $companyField = $row['company'] ?? null;
                $notesField = $row['notes'] ?? null;

                // Skip empty rows
                if (empty($emailField)) {
                    continue;
                }

                // Validate email
                $validator = Validator::make(['email' => $emailField], [
                    'email' => 'required|email'
                ]);

                if ($validator->fails()) {
                    $this->skippedCount++;
                    continue;
                }

                // Check if contact already exists
                if (EmailContact::where('email', $emailField)->where('user_id', $this->userId)->exists()) {
                    $this->skippedCount++;
                    continue;
                }

                // Create contact
                $contact = EmailContact::create([
                    'email' => $emailField,
                    'first_name' => $firstNameField,
                    'last_name' => $lastNameField,
                    'phone' => $phoneField,
                    'company' => $companyField,
                    'notes' => $notesField,
                    'user_id' => $this->userId
                ]);

                // Attach tags if provided
                if (!empty($this->tags)) {
                    $contact->tags()->sync($this->tags);
                }

                $this->importedCount++;

            } catch (Exception $e) {
                $this->skippedCount++;
                continue;
            }
        }
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
}
