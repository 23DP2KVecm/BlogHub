<?php

namespace App\Http\Controllers;

use App\Models\Komentars;
use App\Models\Raksts;
use App\Models\Reakcija;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Raksts::with(['user', 'category', 'tags'])
            ->where('statuss', 'publicets');

        if ($request->filled('meklet')) {
            $q = $request->meklet;
            $query->where(function ($sub) use ($q) {
                $sub->where('virsraksts', 'like', "%{$q}%")
                    ->orWhere('ievads', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategorija')) {
            $query->where('category_id', $request->kategorija);
        }

        if ($request->filled('datums_no')) {
            $query->whereDate('publicets_datums', '>=', $request->datums_no);
        }

        if ($request->filled('datums_lidz')) {
            $query->whereDate('publicets_datums', '<=', $request->datums_lidz);
        }

        if ($request->filled('min_skatijumi')) {
            $query->where('skatijumi', '>=', (int) $request->min_skatijumi);
        }

        match ($request->get('kartojums', 'jaunakie')) {
            'vecakie'     => $query->orderBy('publicets_datums', 'asc'),
            'popularakie' => $query->orderBy('skatijumi', 'desc'),
            default       => $query->orderBy('publicets_datums', 'desc'),
        };

        return response()->json($query->paginate(6));
    }

    public function storeComment(Request $request, int $id): JsonResponse
    {
        $raksts = Raksts::findOrFail($id);
        $request->validate(['saturs' => 'required|string|min:5|max:1000']);

        $komentars = $raksts->komentari()->create([
            'user_id'    => auth()->id(),
            'saturs'     => $request->saturs,
            'apstiprints' => false,
        ]);

        return response()->json($komentars->load('user'), 201);
    }

    public function show(Raksts $raksts): JsonResponse
    {
        abort_if($raksts->statuss !== 'publicets', 404);

        $raksts->increment('skatijumi');
        $raksts->load(['user', 'category', 'tags', 'komentari.user']);

        $manaReakcija = null;
        $token = request()->bearerToken();
        if ($token) {
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $manaReakcija = $raksts->reakcijas()->where('user_id', $user->id)->value('veids');
            }
        }

        return response()->json([
            ...$raksts->toArray(),
            'patik_count'   => $raksts->reakcijas()->where('veids', 'patik')->count(),
            'nepatik_count' => $raksts->reakcijas()->where('veids', 'nepatik')->count(),
            'mana_reakcija' => $manaReakcija,
        ]);
    }

    public function storeReakcija(Request $request, int $id): JsonResponse
    {
        $request->validate(['veids' => 'required|in:patik,nepatik']);

        $esosa = Reakcija::where('post_id', $id)->where('user_id', auth()->id())->first();

        if ($esosa) {
            if ($esosa->veids === $request->veids) {
                $esosa->delete();
            } else {
                $esosa->update(['veids' => $request->veids]);
            }
        } else {
            Reakcija::create(['post_id' => $id, 'user_id' => auth()->id(), 'veids' => $request->veids]);
        }

        return response()->json([
            'patik_count'   => Reakcija::where('post_id', $id)->where('veids', 'patik')->count(),
            'nepatik_count' => Reakcija::where('post_id', $id)->where('veids', 'nepatik')->count(),
            'mana_reakcija' => Reakcija::where('post_id', $id)->where('user_id', auth()->id())->value('veids'),
        ]);
    }
}
