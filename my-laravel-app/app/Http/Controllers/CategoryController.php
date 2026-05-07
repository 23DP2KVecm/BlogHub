<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $kategorijas = Kategorija::withCount([
            'raksti as raksti_count' => fn ($q) => $q->where('statuss', 'publicets'),
        ])->get();

        return response()->json($kategorijas);
    }
}
