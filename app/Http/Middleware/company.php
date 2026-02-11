<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class company
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->type == 'super admin') {
            return $next($request);
        }
        if ($request->user()->plan !== 0) {

            $user = $request->user();
            $trialExpireDate = $user->trial_expire_date;
            $planExpireDate = $user->plan_expire_date;
            $currentDate = now();


            if ($trialExpireDate && $currentDate->greaterThan($trialExpireDate) && $user->type !== 'super admin') {
                return redirect()->route('plans.index')->with('error', __('Please pay the subscription before the grace period ends'));
            }
            // dd($currentDate,$trialExpireDate,$planExpireDate,$currentDate->greaterThan($planExpireDate),$user->type);
            // التحقق من فترة انتهاء الخطة
            if (is_null($trialExpireDate) && $planExpireDate && $currentDate->greaterThan($planExpireDate) && $user->type !== 'super admin') {
                return redirect()->route('plans.index')->with('error', __('Please pay the subscription before the grace period ends'));
            }
        
            return $next($request);

        }else{
            // \Log::info('company');
            return  redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
