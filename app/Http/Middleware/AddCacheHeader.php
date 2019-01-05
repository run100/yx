<?php

namespace App\Http\Middleware;


use App\Http\Response\RedirectMessageResponse;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class AddCacheHeader
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param int|string $life
     * @return mixed
     */
    public function handle($request, Closure $next, $life)
    {
        /** @var Response $response */
        $response = $next($request);

        do {
            if ($response->getStatusCode() != 200) {
                break;
            }

            if ($response instanceof RedirectMessageResponse) {
                break;
            }

            if ($response instanceof RedirectResponse) {
                break;
            }

            if (!$life) {
                break;
            }

            $now = new \DateTime();
            if (is_numeric($life)) {
                $life = (int)$life;
            } elseif (preg_match('@^E(\d+)$@', $life, $m)) {
                $life = (int)$m[1];
                $start = strtotime(date('Y-m-d 00:00:00'));
                $life = $life - ($now->getTimestamp() - $start) % $life;
            } else {
                $expire = new \DateTime($life);
                $life = $expire->getTimestamp() - $now->getTimestamp();
            }

            if ($life > 0) {
                $expire = clone $now;
                $expire->add(new \DateInterval("PT{$life}S"));
                $response->setLastModified($now);
                $response->setMaxAge($life);
                $response->setExpires($expire);
                $response->setPublic();
                $response->headers->set('Pragma', 'public');
            }

        } while(0);


        return $response;
    }
}
