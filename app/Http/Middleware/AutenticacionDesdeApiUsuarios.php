<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;


class AutenticacionDesdeApiUsuarios
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if ($token == null)
            return response(["error" => "Not authenticated"], 401);

        $validacion = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->get(getenv("API_AUTH_URL") . '/api/validate');

        if ($validacion->status() != 200)
            return response(["error" => "Invalid Token"], 401);

        $request->merge(['user' => $validacion->json()]);
        return $next($request);
    }
}