<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Issue;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class Issues extends Component
{
    use WithPagination;

    public $issues, $title, $description, $status, $issue_id, $github_issue_id, $repo_name;
    public $isOpen = false;
    public function mount($repo)
    {
        $this->repo_name = $repo;
        $auth_user = Auth::user();
        $user = User::find($auth_user->id);
        $githubClient = new \App\Support\Github($user);
        $issues = $githubClient->getIssues($this->repo_name);
        foreach ($issues as $issue) {
            Issue::updateOrCreate(['github_issue_id' => $issue->id,'repo_name' => $this->repo_name], [
                'title' => $issue->title,
                'description' => $issue->body,
                'status' => $issue->state,

            ]);
        }
    }
    public function render()
    {

        $this->issues = Issue::where('repo_name',$this->repo_name)->get();

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
            $issue->title = $this->title;
            $issue->description = $this->description;
            $issue->status = $this->status;
            if ($issue->isDirty()) {
                $issue->save();
                session()->flash(
                    'message',
                    'Issue Updated Successfully.'
                );
            } else {

                session()->flash(
                    'message',
                    'Issue was unchanged and saved successfully.'
                );
            }
        } else {

            $issue =  Issue::create([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'repo_name' => $this->repo_name,
            ]);


            session()->flash(
                'message',
                'Issue was created successfully.'
            );
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
}
