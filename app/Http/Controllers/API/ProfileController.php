<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }
}
