<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Runs only when no route matched: an old address listed in `redirects` is forwarded, anything else is 404.
 * An exact match with the query string wins (/federation.html?org=oman-fa), then the bare path.
 */
class RedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $path = Redirect::normalize($request->getPathInfo());
        $query = $request->getQueryString();
        $candidates = array_values(array_unique(array_filter([$query ? $path.'?'.$query : null, $path])));

        $redirect = Redirect::query()->whereIn('from_path', $candidates)->get()
            ->sortBy(fn (Redirect $r) => array_search($r->from_path, $candidates, true))->first();

        abort_if($redirect === null, 404);

        Redirect::query()->whereKey($redirect->getKey())->update(['hits' => $redirect->hits + 1, 'last_hit_at' => now()]);

        return redirect()->to($redirect->to_path, $redirect->status_code);
    }
}
