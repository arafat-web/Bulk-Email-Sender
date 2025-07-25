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
                // Skip empty rows
                if (empty($row['email'])) {
                    continue;
                }

                // Validate email
                $validator = Validator::make($row->toArray(), [
                    'email' => 'required|email'
                ]);

                if ($validator->fails()) {
                    $this->skippedCount++;
                    continue;
                }

                // Check if contact already exists
                if (EmailContact::where('email', $row['email'])->where('user_id', $this->userId)->exists()) {
                    $this->skippedCount++;
                    continue;
                }

                // Create contact
                $contact = EmailContact::create([
                    'email' => $row['email'],
                    'first_name' => $row['first_name'] ?? null,
                    'last_name' => $row['last_name'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'company' => $row['company'] ?? null,
                    'notes' => $row['notes'] ?? null,
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
