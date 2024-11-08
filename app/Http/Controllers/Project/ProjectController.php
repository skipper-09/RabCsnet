<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Project'
        ];

        return view('pages.project.index', $data);
    }
}
