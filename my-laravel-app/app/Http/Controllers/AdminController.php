<?php

namespace App\Http\Controllers;

use App\Models\Komentars;
use App\Models\Raksts;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        return response()->json(
            User::with('role')->orderBy('created_at', 'desc')->get()
        );
    }

    public function updateUserRole(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        $user->update($validated);

        return response()->json($user->load('role'));
    }

    public function roles(): JsonResponse
    {
        return response()->json(Role::all());
    }

    public function posts(): JsonResponse
    {
        return response()->json(
            Raksts::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->paginate(15)
        );
    }

    public function deletePost(int $id): JsonResponse
    {
        Raksts::findOrFail($id)->delete();
        return response()->json(['message' => 'Raksts dzēsts.']);
    }

    public function comments(): JsonResponse
    {
        return response()->json(
            Komentars::with(['user', 'raksts:id,virsraksts,slug'])
                ->orderBy('created_at', 'desc')
                ->paginate(20)
        );
    }

    public function approveComment(int $id): JsonResponse
    {
        $k = Komentars::findOrFail($id);
        $k->update(['apstiprints' => ! $k->apstiprints]);
        return response()->json($k);
    }

    public function deleteComment(int $id): JsonResponse
    {
        Komentars::findOrFail($id)->delete();
        return response()->json(['message' => 'Komentārs dzēsts.']);
    }
}
