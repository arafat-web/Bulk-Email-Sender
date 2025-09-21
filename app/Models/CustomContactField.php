<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomContactField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'type',
        'description',
        'options',
        'default_value',
        'is_required',
        'is_active',
        'sort_order',
        'user_id'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Field types available
     */
    public static $types = [
        'text' => 'Text',
        'number' => 'Number',
        'email' => 'Email',
        'url' => 'URL',
        'textarea' => 'Text Area',
        'select' => 'Select',
        'date' => 'Date'
    ];

    /**
     * Get the user that owns the custom field.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all field values for this custom field.
     */
    public function values(): HasMany
    {
        return $this->hasMany(CustomContactFieldValue::class, 'field_id');
    }

    /**
     * Scope to filter active fields only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::$types[$this->type] ?? $this->type;
    }

    /**
     * Get the validation rule for this field type.
     */
    public function getValidationRule(): string
    {
        $rule = $this->is_required ? 'required' : 'nullable';
        
        switch ($this->type) {
            case 'email':
                return $rule . '|email';
            case 'number':
                return $rule . '|numeric';
            case 'url':
                return $rule . '|url';
            case 'date':
                return $rule . '|date';
            case 'select':
                if (!empty($this->options)) {
                    $options = implode(',', array_keys($this->options));
                    return $rule . '|in:' . $options;
                }
                return $rule . '|string';
            default:
                return $rule . '|string';
        }
    }
}
