<?php

namespace App\Http\Controllers;

use App\Models\Birka;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Birka::orderBy('nosaukums')->get());
    }
}
