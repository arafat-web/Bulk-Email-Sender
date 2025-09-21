<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomContactFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'field_id',
        'value'
    ];

    /**
     * Get the contact that owns this field value.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(EmailContact::class, 'contact_id');
    }

    /**
     * Get the custom field definition.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomContactField::class, 'field_id');
    }

    /**
     * Get the formatted value based on field type.
     */
    public function getFormattedValueAttribute()
    {
        if (empty($this->value)) {
            return null;
        }

        switch ($this->field->type) {
            case 'date':
                return \Carbon\Carbon::parse($this->value)->format('Y-m-d');
            case 'select':
                // Return the option label if available
                if (!empty($this->field->options) && isset($this->field->options[$this->value])) {
                    return $this->field->options[$this->value];
                }
                return $this->value;
            default:
                return $this->value;
        }
    }
}
