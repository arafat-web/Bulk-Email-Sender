<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\TempMailAddress;

class ImportData implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Map new column names to database fields - support both new and old formats
        $emailField = $row['email_address'] ?? $row['email'] ?? null;
        $firstNameField = $row['given_name'] ?? $row['first_name'] ?? null;
        $lastNameField = $row['family_name'] ?? $row['last_name'] ?? null;
        $phoneField = $row['phone'] ?? null;
        $companyField = $row['company'] ?? null;
        $notesField = $row['notes'] ?? null;

        // Skip rows without email
        if (empty($emailField)) {
            return null;
        }

        return new TempMailAddress([
            'email' => $emailField,
            'first_name' => $firstNameField,
            'last_name' => $lastNameField,
            'phone' => $phoneField,
            'company' => $companyField,
            'notes' => $notesField,
        ]);
    }
}
