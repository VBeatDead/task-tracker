<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('id')->get();

        return response()->json(['data' => $categories, 'message' => 'OK'], 200);
    }
}
