<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class AdminCheck.
 */
class AdminCheck
{
    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $type = $request->user()->type;
        if ($request->user() && in_array($type,$roles)) {
            return $next($request);
        }

        return redirect()->route('frontend.index')->withFlashDanger(__('You do not have access to do that.'));
    }
}
