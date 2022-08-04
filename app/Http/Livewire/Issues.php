<?php

namespace App\Http\Livewire;

use App\Models\Issue;
use Livewire\Component;

class Issues extends Component
{
    public $issues, $title, $description, $status, $issue_id, $github_issue_id, $repo_name;
    public $isOpen = false;
    public function render()
    {
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
            'github_issue_id' => 'required',
            'repo_name' => 'required',
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
        }else{

        }
        Issue::create([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'github_issue_id' => $this->github_issue_id,
            'repo_name' => $this->repo_name,
        ]);

        session()->flash(
            'message',
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $issue = Issue::findOrFail($id);
        $this->issue_id = $id;
        $this->title = $issue->title;
        $this->description = $issue->description;
        $this->status = $issue->status;
        $this->openModal();
    }
}
