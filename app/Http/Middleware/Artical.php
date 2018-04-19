<?php

namespace App\Http\Middleware;

use App\Models\ArticalExpert;
use App\Models\Auth;
use Closure;
use Encore\Admin\Facades\Admin;

class Artical
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::isLecturer()) {
            $ArticalExpert=ArticalExpert::find(Admin::user()->id);
            if(!$ArticalExpert){
                die('你还不是作者，请工作人员帮助开通作者');
            }
        }
        return $next($request);
    }
}
