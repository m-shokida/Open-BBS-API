<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FileIsValid
{
    /**
     * ファイルが正常にアップロードされているか
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        foreach (array_keys($request->all()) as $key) {
            if ($request->hasFile($key) && !$request->file($key)->isValid()) {
                Log::warning('Possible file upload attack : ' . $request->ip());
                return response()->json(status: Response::HTTP_BAD_REQUEST);
            }
        }
        return $next($request);
    }
}
