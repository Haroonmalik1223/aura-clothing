<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     * Note: We exclude HEADER_X_FORWARDED_PORT so internal proxy port 80
     * does not get appended to public HTTPS URLs.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $request::setTrustedProxies([], $this->getTrustedHeaderNames());
        $this->setTrustedProxyIpAddresses($request);

        $isHttps = $request->isSecure()
            || $request->header('x-forwarded-proto') === 'https'
            || ($request->header('cf-visitor') && str_contains($request->header('cf-visitor'), 'https'))
            || config('app.env') === 'production';

        if ($isHttps) {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
            \Illuminate\Support\Facades\URL::forceScheme('https');

            $host = $request->getHost();
            if ($host && !in_array($host, ['localhost', '127.0.0.1'])) {
                \Illuminate\Support\Facades\URL::forceRootUrl('https://' . $host);
            }
        }

        return $next($request);
    }

    /**
     * Override to exclude HEADER_X_FORWARDED_PORT so internal reverse proxy port 80
     * is never appended to public HTTPS URLs.
     *
     * @return int
     */
    protected function getTrustedHeaderNames()
    {
        return Request::HEADER_X_FORWARDED_FOR |
               Request::HEADER_X_FORWARDED_HOST |
               Request::HEADER_X_FORWARDED_PROTO |
               Request::HEADER_X_FORWARDED_AWS_ELB;
    }
}
