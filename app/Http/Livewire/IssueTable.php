<?php

namespace App\Http\Livewire;

use App\Models\Issue;
use Livewire\Component;

class IssueTable extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];

    public $issues;

    public function mount($issues)
    {
        $this->issues = $issues;
        // $this->issues = Issue::where('repo_name', $this->repo_name)->orderBy('created_at', 'desc')->get();
    }
    // public function getListeners()
    // {
    //     return array_merge($this->listeners, [
    //         "isue-{$this->repo_name}" => '$refresh',
    //     ]);
    // }

    public function render()
    {

        return view('livewire.issue-table',['data'=>$this->issues]);
    }

    public function edit($id)
    {
        $this->emit('edit', $id);
    }
    public function delete($id)
    {
        $this->emit('delete', $id);
    }
}
