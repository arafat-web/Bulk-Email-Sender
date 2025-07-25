<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
        'user_id'
    ];

    /**
     * Get the user that owns the tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The contacts that belong to the tag.
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(EmailContact::class, 'email_contact_tag_pivot');
    }

    /**
     * Get the contact count for this tag.
     */
    public function getContactCountAttribute(): int
    {
        return $this->contacts()->count();
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}
