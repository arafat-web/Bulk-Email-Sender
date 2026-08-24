<?php

namespace App\Imports;

use App\Models\TempMailAddress;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportData implements ToModel, WithHeadingRow
{
    protected ?int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId ?? (auth()->check() ? auth()->id() : null);
    }

    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        // Map column names to database fields - support both new and old formats
        $emailField = trim($row['email_address'] ?? $row['email'] ?? '');
        $firstNameField = $row['given_name'] ?? $row['first_name'] ?? null;
        $lastNameField = $row['family_name'] ?? $row['last_name'] ?? null;
        $phoneField = $row['phone'] ?? null;
        $companyField = $row['company'] ?? null;
        $notesField = $row['notes'] ?? null;

        // Skip rows without valid email
        if (empty($emailField) || ! filter_var($emailField, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return new TempMailAddress([
            'user_id' => $this->userId,
            'email' => strtolower(trim($emailField)),
            'first_name' => $firstNameField,
            'last_name' => $lastNameField,
            'phone' => $phoneField,
            'company' => $companyField,
            'notes' => $notesField,
        ]);
    }
}
