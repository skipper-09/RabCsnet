<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Dashboard'
        ];

        return view('pages.dashboard.index', $data);
    }
}