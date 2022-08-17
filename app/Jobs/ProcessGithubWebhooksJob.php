<?php

namespace App\Jobs;

use App\Models\Issue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Spatie\GitHubWebhooks\Models\GitHubWebhookCall;

class ProcessGithubWebhooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public GitHubWebhookCall $webhookCall)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $action = $this->webhookCall->eventActionName();
        $payload = $this->webhookCall->payload();
        /**
         *  Issue was created event
         */
        if ($action == 'issues.opened') {
            $issue = $payload['issue'];
            $repository = $payload['repository'];
            Issue::create([
                'title' => $issue['title'],
                'description' => $issue['body'],
                'github_issue_id' => $issue['number'],
                'status' => $issue['state'],
                'repo_name' => $repository['name']
            ]);
        }
        /**
         *  Issue was updated
         */
        if ($action == 'issues.edited') {
            $issue = $payload['issue'];
            $repository = $payload['repository'];

            $local_issue = Issue::where('github_issue_id', $issue['number'])->where('repo_name', $repository['name'])->first();
            $local_issue->title = $issue['title'];
            $local_issue->description = $issue['body'];
            $local_issue->status = $issue['state'];
            $local_issue->save();
        }
        /**
         *  Issue was closed event
         */
        if ($action == 'issues.closed') {
            $issue = $payload['issue'];
            $repository = $payload['repository'];
            $local_issue = Issue::where('github_issue_id', $issue['number'])->where('repo_name', $repository['name'])->first();
            $local_issue->status = 'closed';
            $local_issue->save();
        }
        /**
         *  Issue was deleted event
         */
        if ($action == 'issues.locked') {
            $issue = $payload['issue'];
            $repository = $payload['repository'];
            Issue::where('github_issue_id', $issue['number'])->where('repo_name', $repository['name'])->delete();
        }
    }
}
