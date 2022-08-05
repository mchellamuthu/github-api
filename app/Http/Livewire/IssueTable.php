<?php

namespace App\Http\Livewire;

use Livewire\Component;

class IssueTable extends Component
{
    protected $listeners = ['refreshComponent'=>'$refresh'];
    public $issues;

    public function mount($issues)
    {
        $this->issues = $issues;
    }
    public function render()
    {

        return view('livewire.issue-table');
    }

    public function edit($id){
        $this->emit('edit',$id);
    }
    public function delete($id){
        $this->emit('delete',$id);
    }
}
