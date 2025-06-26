<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get the user's IP address.
     */
    public static function getUserIp()
    {
        return request()->ip();
    }

    /**
     * Check if the given IP is allowed for public access.
     */
    public static function isIpAllowed($ip = null)
    {
        $ip = $ip ?: request()->ip();
        $allowedIPs = config('app.allowed_ips', []);
        return in_array($ip, $allowedIPs);
    }

    /**
     * Get all allowed IPs for public access.
     */
    public static function getAllowedIPs()
    {
        return config('app.allowed_ips', []);
    }
}
