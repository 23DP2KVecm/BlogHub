<?php

namespace App\Http\Controllers;

use App\Models\Raksts;
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

    public function show(Raksts $raksts): JsonResponse
    {
        abort_if($raksts->statuss !== 'publicets', 404);

        $raksts->increment('skatijumi');

        return response()->json(
            $raksts->load(['user', 'category', 'tags', 'komentari.user'])
        );
    }
}
