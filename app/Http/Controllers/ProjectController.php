<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        Project::create([
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        return redirect('/projects');
    }
}
