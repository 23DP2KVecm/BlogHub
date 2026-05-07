<?php

namespace App\Http\Controllers;

use App\Models\Birka;
use App\Models\Raksts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $raksti = Raksts::with(['category', 'tags'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($raksti);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'virsraksts'  => 'required|string|max:200',
            'saturs'      => 'required|string|min:10',
            'ievads'      => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:kategorijas,id',
            'statuss'     => 'required|in:melnraksts,publicets',
            'birkas'      => 'nullable|array',
            'birkas.*'    => 'exists:birkas,id',
        ]);

        $slug = $this->uniqueSlug($validated['virsraksts']);

        $raksts = Raksts::create([
            'user_id'          => auth()->id(),
            'category_id'      => $validated['category_id'] ?? null,
            'virsraksts'       => $validated['virsraksts'],
            'slug'             => $slug,
            'saturs'           => $validated['saturs'],
            'ievads'           => $validated['ievads'] ?? null,
            'statuss'          => $validated['statuss'],
            'publicets_datums' => $validated['statuss'] === 'publicets' ? now() : null,
        ]);

        if (! empty($validated['birkas'])) {
            $raksts->tags()->sync($validated['birkas']);
        }

        return response()->json($raksts->load(['category', 'tags', 'user']), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $raksts = Raksts::findOrFail($id);

        if ($raksts->user_id !== auth()->id() && auth()->user()->role?->nosaukums !== 'administrators') {
            return response()->json(['message' => 'Nav tiesību rediģēt šo rakstu.'], 403);
        }

        $validated = $request->validate([
            'virsraksts'  => 'required|string|max:200',
            'saturs'      => 'required|string|min:10',
            'ievads'      => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:kategorijas,id',
            'statuss'     => 'required|in:melnraksts,publicets,arhivets',
            'birkas'      => 'nullable|array',
            'birkas.*'    => 'exists:birkas,id',
        ]);

        $wasPublished = $raksts->statuss === 'publicets';
        $nowPublished = $validated['statuss'] === 'publicets';

        $raksts->update([
            'category_id'      => $validated['category_id'] ?? null,
            'virsraksts'       => $validated['virsraksts'],
            'saturs'           => $validated['saturs'],
            'ievads'           => $validated['ievads'] ?? null,
            'statuss'          => $validated['statuss'],
            'publicets_datums' => (! $wasPublished && $nowPublished) ? now() : $raksts->publicets_datums,
        ]);

        $raksts->tags()->sync($validated['birkas'] ?? []);

        return response()->json($raksts->load(['category', 'tags', 'user']));
    }

    public function destroy(int $id): JsonResponse
    {
        $raksts = Raksts::findOrFail($id);

        if ($raksts->user_id !== auth()->id() && auth()->user()->role?->nosaukums !== 'administrators') {
            return response()->json(['message' => 'Nav tiesību dzēst šo rakstu.'], 403);
        }

        $raksts->delete();

        return response()->json(['message' => 'Raksts dzēsts.']);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;
        while (Raksts::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
