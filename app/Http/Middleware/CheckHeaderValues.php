<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Http\Traits\ApiResponseTrait;

class CheckHeaderValues
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (JsonResponse) $next
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if ($request->header('Content-Type') !== 'application/json' && $request->header('Accept') !== 'application/json') {
            return $this->error('Only Support JSON', [], HttpResponse::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
