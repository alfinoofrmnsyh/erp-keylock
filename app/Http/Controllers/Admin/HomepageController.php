<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        return response()->json(
            Homepage::all()
        );
    }
}