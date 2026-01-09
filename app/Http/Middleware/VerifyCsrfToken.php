<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;


class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/isUserNameUnique',
        '/getPasswordHint',
        '/setVesselLocation',
        '/removeVesselLocation',
        '/getGameStatus',
        '/markAsRead',
        '/getLatestOpponentMove',
        '/strikeVesselLocation',
        '/replaceFleetVesselLocations',
        '/removeAllVesselLocations',
    ];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (last(explode('\\',get_class($response))) != 'RedirectResponse') {
            //$response->header('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        }

        return $response;
    }
}
