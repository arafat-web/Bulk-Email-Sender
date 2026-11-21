<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailUnsubscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'reason',
        'source',
        'unsubscribed_at',
    ];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Normalize an email for comparison/storage.
     */
    public static function normalize(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Check if an email is unsubscribed (global suppression list
     * OR contact status = unsubscribed).
     */
    public static function isUnsubscribed(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        $email = static::normalize($email);

        if (static::where('email', $email)->exists()) {
            return true;
        }

        return EmailContact::where('email', $email)
            ->where('status', 'unsubscribed')
            ->exists();
    }

    /**
     * Unsubscribe an email: add to suppression list + flip contact status.
     */
    public static function unsubscribe(string $email, string $source = 'footer_link', ?string $reason = null): self
    {
        $email = static::normalize($email);

        $record = static::updateOrCreate(
            ['email' => $email],
            [
                'source' => $source,
                'reason' => $reason,
                'unsubscribed_at' => now(),
            ]
        );

        EmailContact::where('email', $email)->update(['status' => 'unsubscribed']);

        return $record;
    }

    /**
     * Re-subscribe (used by manual admin actions).
     */
    public static function resubscribe(string $email): void
    {
        $email = static::normalize($email);

        static::where('email', $email)->delete();
    }
}
