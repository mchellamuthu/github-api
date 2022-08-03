<?php

namespace App\Support;

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
        $response = $client->request('GET', 'https://api.github.com/repos/' . $this->user->name . '/'.$repoId.'/issues', $options);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }
}
