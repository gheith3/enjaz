<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SiteLock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** The 6-digit code of the «قريبًا» page: right code = a cookie that opens the site for 30 days. */
class UnlockController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->merge(['code' => SiteLock::latinDigits($request->input('code'))]);   // typed on an Arabic keyboard
        $request->validate(['code' => ['required', 'digits:6']], ['code.*' => __('site.lock.wrong')]);
        $settings = SiteLock::settings();

        if (! $settings || ! $settings->enabled || blank($settings->code) || ! hash_equals(SiteLock::latinDigits($settings->code), (string) $request->input('code'))) {
            return redirect('/#sign')->withErrors(['code' => __('site.lock.wrong')]);
        }

        return redirect('/')->withCookie(cookie(SiteLock::COOKIE, SiteLock::token($settings->code), 60 * 24 * 30));
    }
}
