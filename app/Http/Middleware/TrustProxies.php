<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Get the trusted proxies.
     *
     * Allows TRUSTED_PROXIES env override (comma-separated IPs/CIDRs).
     * Defaults to '*' which trusts only the calling IP (the LB/proxy).
     */
    protected function proxies(): array|string|null
    {
        return env('TRUSTED_PROXIES', '*');
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
