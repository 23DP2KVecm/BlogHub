<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(Request $request, Closure $next, string $role = ''): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Nav autorizācijas.'], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (! $user) {
            return response()->json(['message' => 'Nepareizs vai beidzies tokens.'], 401);
        }

        if ($role === 'administrators' && $user->role?->nosaukums !== 'administrators') {
            return response()->json(['message' => 'Nepietiekamas tiesības.'], 403);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
