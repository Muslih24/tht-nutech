<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $title = "Dashboard";
        $data = [
            'title' => $title,
            'auth' => $auth
        ];
        return view("dashboard.index", $data);
    }
}
