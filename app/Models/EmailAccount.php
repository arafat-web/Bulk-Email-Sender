<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EmailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_name',
        'is_default',
        'is_active',
        'last_used_at',
        'emails_sent',
        'notes',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'emails_sent' => 'integer',
    ];

    // Encrypt password when setting
    public function setSmtpPasswordAttribute($value)
    {
        $this->attributes['smtp_password'] = Crypt::encryptString($value);
    }

    // Decrypt password when getting - handles legacy plaintext values gracefully
    public function getSmtpPasswordAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Legacy plaintext password stored before encryption was added
            Log::warning('EmailAccount legacy plaintext password detected', ['account_id' => $this->id ?? 'unknown']);

            return $value;
        }
    }

    // Scope to get only active accounts
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get default account
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->where('is_active', true);
    }

    // Set this account as default (and unset others)
    public function setAsDefault()
    {
        // First, unset all other default accounts
        self::where('id', '!=', $this->id)->update(['is_default' => false]);

        // Then set this one as default
        $this->update(['is_default' => true, 'is_active' => true]);
    }

    // Increment emails sent count
    public function incrementEmailsSent()
    {
        $this->increment('emails_sent');
        $this->update(['last_used_at' => now()]);
    }

    // Get the default email account
    public static function getDefault()
    {
        return self::default()->first();
    }
}
