<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class Github
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAllRepositories()
    {
        $client = new \GuzzleHttp\Client();
        $options = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/vnd.github+json"'
        ];
        $response = $client->request('GET', 'https://api.github.com/users/' . $this->user->name . '/repos?sort=created&direction=desc', $options);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
    public function getIssues($repoId)
    {
        $uri  = 'https://api.github.com/repos/' . $this->user->name . '/' . $repoId . '/issues';

        $headers = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/vnd.github+json"'
        ];
        $response = Http::withHeaders($headers)->get($uri);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
    public function createIssue($repoId, $data = array())
    {
        $uri = 'https://api.github.com/repos/' . $this->user->github_nickname . '/' . $repoId . '/issues';

        $headers = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post($uri, $data);

        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
    public function updateIssue($repoId, $data = array(), $issue_id)
    {
        $uri = 'https://api.github.com/repos/' . $this->user->github_nickname . '/' . $repoId . '/issues/' . $issue_id;
        // dump($uri);
        $headers = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->patch($uri, $data);

        $response = json_decode($response->getBody()->getContents());
        return $response;


    }
    public function deleteIssue($repoId, $issue_id)
    {
        $uri = 'https://api.github.com/repos/' . $this->user->github_nickname . '/' . $repoId . '/issues/' . $issue_id . '/lock';
        // dump($uri);
        $headers = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->put($uri, ['lock_reason' => 'spam']);

        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
}
