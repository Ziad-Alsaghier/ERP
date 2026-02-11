<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class ghost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$redirectToRoute = null): Response
    {

        if (! $request->user() || ($request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail())) {

            return $request->expectsJson() ? abort(403, 'Your email address is not verified.') : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));

        }else{
            return $next($request);
        }
    }
}
