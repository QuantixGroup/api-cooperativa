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
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['mensaje' => 'Falta el token de acceso'], 401);
        }

        $url = config('services.usuarios.me'); 
        $response = Http::withToken($token)->acceptJson()->get($url);

        if (!$response->ok()) {
            return response()->json(['mensaje' => 'Token inválido o vencido'], 401);
        }

        $usuario = $response->json(); 
        $request->attributes->set('id_usuario_autenticado', $usuario['id'] ?? null);
        $request->attributes->set('cedula_usuario_autenticado', $usuario['cedula'] ?? null);

        return $next($request);
    }
}
