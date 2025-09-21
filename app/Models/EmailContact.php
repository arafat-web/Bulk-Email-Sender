<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'phone',
        'company',
        'notes',
        'status',
        'last_emailed_at',
        'user_id'
    ];

    protected $casts = [
        'last_emailed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tags that belong to the contact.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ContactTag::class, 'email_contact_tag_pivot');
    }

    /**
     * Get custom field values for this contact.
     */
    public function customFieldValues()
    {
        return $this->hasMany(CustomContactFieldValue::class, 'contact_id');
    }

    /**
     * Get a specific custom field value.
     */
    public function getCustomField(string $fieldName)
    {
        $fieldValue = $this->customFieldValues()
            ->whereHas('field', function ($query) use ($fieldName) {
                $query->where('name', $fieldName)->where('user_id', $this->user_id);
            })
            ->with('field')
            ->first();

        return $fieldValue ? $fieldValue->value : null;
    }

    /**
     * Set a custom field value.
     */
    public function setCustomField(string $fieldName, $value)
    {
        $field = CustomContactField::where('name', $fieldName)
            ->where('user_id', $this->user_id)
            ->first();

        if (!$field) {
            return false;
        }

        return CustomContactFieldValue::updateOrCreate(
            [
                'contact_id' => $this->id,
                'field_id' => $field->id,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * Get all custom fields as key-value pairs.
     */
    public function getCustomFieldsAttribute()
    {
        $customFields = [];
        
        foreach ($this->customFieldValues()->with('field')->get() as $fieldValue) {
            $customFields[$fieldValue->field->name] = $fieldValue->value;
        }

        return $customFields;
    }

    /**
     * Get the contact's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by tag.
     */
    public function scopeWithTag($query, $tagId)
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('contact_tags.id', $tagId);
        });
    }

    /**
     * Scope to search by email or name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%");
        });
    }
}
