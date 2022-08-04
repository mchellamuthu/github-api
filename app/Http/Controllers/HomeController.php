<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = Auth::user();
        $user = User::find($auth_user->id);
        $githubClient = new \App\Support\Github($user);
        $repos = $githubClient->getAllRepositories();
        // dd($repos);
        return view('dashboard',compact('repos'));
    }
    public function getIssues($repo)
    {
        $auth_user = Auth::user();
        $user = User::find($auth_user->id);
        $githubClient = new \App\Support\Github($user);
        $issues = $githubClient->createIssue($repo,['title'=>'new issue from testing','body'=>'issue']);
        dd($issues);
        // return view('dashboard',compact('repos'));
    }
}
