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
        $client = new \GuzzleHttp\Client();
        $options = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/vnd.github+json"'
        ];
        $response = $client->request('GET', 'https://api.github.com/repos/' . $this->user->name . '/' . $repoId . '/issues', $options);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
    public function createIssue($repoId, $data = array())
    {
        $uri = 'https://api.github.com/repos/' . $this->user->github_nickname . '/' . $repoId . '/issues';

        // dd($uri);
        $headers = [
            'Authorization' => 'token ' . auth()->user()->github_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        // dump($uri);
        // dd($headers);
        // $client = new \GuzzleHttp\Client();
        // $client->request($uri, json_encode($data));
        $response = Http::withHeaders($headers)->post($uri, $data);

        // dd($response);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
}
