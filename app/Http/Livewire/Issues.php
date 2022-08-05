<?php

namespace App\Http\Livewire;

use Exception;
use App\Models\User;
use App\Models\Issue;
use App\Support\Github;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class Issues extends Component
{
    use WithPagination;

    public $title, $description, $status, $issue_id, $github_issue_id, $repo_name;

    protected $listeners = ['edit','store','delete'];
    public  $issues;
    public $isOpen = false;

    public $user;
    public function mount($repo)
    {
        $auth_user = Auth::user();
        $user = User::find($auth_user->id);
        $this->user = $user;
        $this->repo_name = $repo;

        $repo_issues =  Issue::where('repo_name', $this->repo_name)->count();
        if ($repo_issues  == 0) {
            $githubClient = new Github($user);
            $issues = $githubClient->getIssues($this->repo_name);
            foreach ($issues as $issue) {
                Issue::updateOrCreate(['github_issue_id' => $issue->number, 'repo_name' => $this->repo_name], [
                    'title' => $issue->title,
                    'description' => $issue->body,
                    'status' => $issue->state,

                ]);
            }
        }
    }
    public function render()
    {

        $this->issues = Issue::where('repo_name', $this->repo_name)->orderBy('created_at','desc')->get();

        return view('livewire.issues');
    }
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {

        $this->isOpen = false;
    }
    private function resetInputFields()
    {
        $this->title = '';
        $this->description = '';
        $this->status = '';
        $this->issue_id = '';
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required|in:open,closed',
        ]);
        if (!empty($this->issue_id)) {
            $issue = Issue::find($this->issue_id);

            $github = new Github($this->user);
            $response =  $github->updateIssue($this->repo_name, [
                'title' => $this->title,
                'body' => $this->description,
                'state' => $this->status
            ], $issue->github_issue_id);
            // dd($response);
            $this->emit('refreshComponent');

            session()->flash(
                'message',
                'Issue Updated Successfully.'
            );
        } else {



            try {
                $github = new Github($this->user);
                $response =  $github->createIssue($this->repo_name, [
                    'title' => $this->title,
                    'body' => $this->description
                ]);
                $this->emit('refreshComponent');

                session()->flash(
                    'message',
                    'Issue was created successfully.'
                );
            } catch (Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $issue = Issue::findOrFail($id);
        $this->issue_id = $id;
        $this->title = $issue->title;
        $this->description = $issue->description;
        $this->github_issue_id = $issue->github_issue_id;
        $this->status = $issue->status;
        $this->openModal();
    }
    public function delete($id)
    {
        $issue = Issue::findOrFail($id);

    }
}
