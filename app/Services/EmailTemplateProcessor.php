<?php

namespace App\Services;

use App\Models\EmailContact;
use App\Models\CustomContactField;

class EmailTemplateProcessor
{
    /**
     * Process email template with contact data and custom fields.
     */
    public function processTemplate(string $template, EmailContact $contact = null, array $additionalData = []): string
    {
        $processed = $template;

        // Standard contact field replacements
        if ($contact) {
            $replacements = [
                '{{first_name}}' => $contact->first_name ?? '',
                '{{last_name}}' => $contact->last_name ?? '',
                '{{full_name}}' => $contact->full_name ?? '',
                '{{email}}' => $contact->email ?? '',
                '{{phone}}' => $contact->phone ?? '',
                '{{company}}' => $contact->company ?? '',
            ];

            // Replace standard fields
            foreach ($replacements as $placeholder => $value) {
                $processed = str_replace($placeholder, $value, $processed);
            }

            // Replace custom fields
            $customFields = $contact->customFieldValues()->with('field')->get();
            foreach ($customFields as $fieldValue) {
                $placeholder = '{{' . $fieldValue->field->name . '}}';
                $value = $fieldValue->value ?? $fieldValue->field->default_value ?? '';
                $processed = str_replace($placeholder, $value, $processed);
            }
        }

        // Additional data replacements (for instant campaigns, etc.)
        foreach ($additionalData as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $processed = str_replace($placeholder, $value, $processed);
        }

        return $processed;
    }

    /**
     * Process email template for plain email (without contact).
     */
    public function processPlainTemplate(string $template, string $email, array $additionalData = []): string
    {
        $processed = $template;

        // Basic email replacement
        $processed = str_replace('{{email}}', $email, $processed);

        // Additional data replacements
        foreach ($additionalData as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $processed = str_replace($placeholder, $value, $processed);
        }

        // Remove any remaining placeholders that weren't replaced
        $processed = preg_replace('/\{\{[^}]+\}\}/', '', $processed);

        return $processed;
    }

    /**
     * Get available placeholders for a user.
     */
    public function getAvailablePlaceholders(int $userId): array
    {
        $placeholders = [
            'standard' => [
                '{{first_name}}' => 'Contact\'s first name',
                '{{last_name}}' => 'Contact\'s last name',
                '{{full_name}}' => 'Contact\'s full name',
                '{{email}}' => 'Contact\'s email address',
                '{{phone}}' => 'Contact\'s phone number',
                '{{company}}' => 'Contact\'s company',
            ],
            'custom' => []
        ];

        // Get custom fields for this user
        $customFields = CustomContactField::where('user_id', $userId)
            ->active()
            ->ordered()
            ->get();

        foreach ($customFields as $field) {
            $placeholders['custom']['{{' . $field->name . '}}'] = $field->label . ' (' . $field->type_label . ')';
        }

        return $placeholders;
    }

    /**
     * Validate template for placeholder syntax.
     */
    public function validateTemplate(string $template): array
    {
        $errors = [];
        
        // Check for malformed placeholders
        preg_match_all('/\{[^}]*\}/', $template, $matches);
        foreach ($matches[0] as $match) {
            if (!preg_match('/^\{\{[a-zA-Z_][a-zA-Z0-9_]*\}\}$/', $match)) {
                $errors[] = "Invalid placeholder format: {$match}. Use {{field_name}} format.";
            }
        }

        return $errors;
    }

    /**
     * Preview template with sample data.
     */
    public function previewTemplate(string $template, int $userId): string
    {
        $sampleData = [
            '{{first_name}}' => 'John',
            '{{last_name}}' => 'Doe', 
            '{{full_name}}' => 'John Doe',
            '{{email}}' => 'john.doe@example.com',
            '{{phone}}' => '+1 (555) 123-4567',
            '{{company}}' => 'Example Corp',
        ];

        // Add custom field sample data
        $customFields = CustomContactField::where('user_id', $userId)
            ->active()
            ->get();

        foreach ($customFields as $field) {
            $sampleValue = $this->getSampleValueForField($field);
            $sampleData['{{' . $field->name . '}}'] = $sampleValue;
        }

        $processed = $template;
        foreach ($sampleData as $placeholder => $value) {
            $processed = str_replace($placeholder, $value, $processed);
        }

        return $processed;
    }

    /**
     * Generate sample value for a custom field.
     */
    private function getSampleValueForField(CustomContactField $field): string
    {
        if ($field->default_value) {
            return $field->default_value;
        }

        switch ($field->type) {
            case 'text':
                return 'Sample Text';
            case 'number':
                return '123';
            case 'email':
                return 'sample@example.com';
            case 'url':
                return 'https://example.com';
            case 'textarea':
                return 'Sample long text content...';
            case 'select':
                if ($field->options && is_array($field->options)) {
                    return array_values($field->options)[0] ?? 'Option 1';
                }
                return 'Option 1';
            case 'date':
                return date('Y-m-d');
            default:
                return 'Sample Value';
        }
    }
}
