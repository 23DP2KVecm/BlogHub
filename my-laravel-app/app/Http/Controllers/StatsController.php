<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use App\Models\Komentars;
use App\Models\Raksts;
use App\Models\Reakcija;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $kopsavilkums = [
            'raksti'    => Raksts::where('statuss', 'publicets')->count(),
            'autori'    => User::count(),
            'komentari' => Komentars::where('apstiprints', true)->count(),
            'skatijumi' => (int) Raksts::sum('skatijumi'),
        ];

        $paKategorijam = Kategorija::withCount([
            'raksti as raksti_count' => fn ($q) => $q->where('statuss', 'publicets'),
        ])->orderBy('raksti_count', 'desc')->get()
          ->map(fn ($k) => ['nosaukums' => $k->nosaukums, 'skaits' => $k->raksti_count, 'krasa' => $k->krasa]);

        $paMenešiem = Raksts::where('statuss', 'publicets')
            ->where('publicets_datums', '>=', now()->subMonths(6))
            ->select(DB::raw("strftime('%Y-%m', publicets_datums) as menesis"), DB::raw('COUNT(*) as skaits'))
            ->groupBy('menesis')
            ->orderBy('menesis')
            ->get();

        $reakcijas = [
            'patik'    => Reakcija::where('veids', 'patik')->count(),
            'nepatik'  => Reakcija::where('veids', 'nepatik')->count(),
        ];

        $populari = Raksts::with(['user', 'category'])
            ->where('statuss', 'publicets')
            ->orderBy('skatijumi', 'desc')
            ->limit(5)
            ->get(['id', 'virsraksts', 'slug', 'skatijumi', 'user_id', 'category_id']);

        return response()->json(compact(
            'kopsavilkums',
            'paKategorijam',
            'paMenešiem',
            'reakcijas',
            'populari'
        ));
    }
}
