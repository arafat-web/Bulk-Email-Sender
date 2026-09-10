<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmailKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'source_column',
        'default_value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const SOURCES = [
        'email' => 'Email Address',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'full_name' => 'Full Name',
        'phone' => 'Phone',
        'company' => 'Company',
        'notes' => 'Notes',
    ];

    /**
     * Normalize a raw key: lowercase, spaces/dashes -> underscores, keep a-z0-9_.
     */
    public static function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = trim($key, '[]');
        $key = preg_replace('/[\s\-]+/', '_', $key);
        $key = preg_replace('/[^a-z0-9_]/', '', $key);

        return $key;
    }

    /**
     * Display form: [key]
     */
    public function getPlaceholderAttribute(): string
    {
        return '['.$this->key.']';
    }

    /**
     * All active keywords, cached for fast per-email replacement.
     * Returns key => EmailKeyword map.
     */
    public static function activeMap(): array
    {
        return Cache::remember('email_keywords.active_map', 300, function () {
            return static::where('is_active', true)->orderBy('key')->get()->keyBy('key')->all();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('email_keywords.active_map');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolve the value for one keyword for a given recipient row.
     * $row may be an EmailContact, TempMailAddress, or plain array.
     */
    public function resolveFor(mixed $row): string
    {
        $value = null;

        if (! empty($this->source_column)) {
            $col = $this->source_column;

            if (is_array($row)) {
                $value = $row[$col] ?? null;
            } elseif (is_object($row)) {
                if ($col === 'full_name' && method_exists($row, 'getFullNameAttribute')) {
                    $value = $row->full_name ?? $row->getFullNameAttribute();
                } else {
                    $value = $row->{$col} ?? null;
                }
            }

            // full_name fallback: build from first + last
            if (($value === null || $value === '') && $col === 'full_name') {
                $first = is_array($row) ? ($row['first_name'] ?? '') : ($row->first_name ?? '');
                $last = is_array($row) ? ($row['last_name'] ?? '') : ($row->last_name ?? '');
                $full = trim(($first ?? '').' '.($last ?? ''));
                if ($full !== '') {
                    $value = $full;
                }
            }

            // name alias: prefer full name, then first name
            if (($value === null || $value === '') && $col === 'name') {
                $value = is_array($row)
                    ? ($row['full_name'] ?? $row['first_name'] ?? null)
                    : ($row->full_name ?? $row->first_name ?? null);
            }
        }

        if ($value === null || $value === '') {
            $value = $this->default_value ?? '';
        }

        return (string) $value;
    }

    /**
     * Replace every active [keyword] in $text using recipient $row data.
     * Unknown placeholders are left untouched.
     */
    public static function replaceIn(string $text, mixed $row = null, ?array $keywords = null): string
    {
        $keywords ??= static::activeMap();

        if (empty($keywords) || $text === '' || ! str_contains($text, '[')) {
            return $text;
        }

        return preg_replace_callback('/\[([a-zA-Z0-9_\-]+)\]/', function ($m) use ($keywords, $row) {
            $raw = $m[1];
            $normalized = str_replace('-', '_', strtolower($raw));

            if (! isset($keywords[$normalized])) {
                return $m[0]; // leave unknown placeholders as-is
            }

            return $keywords[$normalized]->resolveFor($row);
        }, $text);
    }
}
