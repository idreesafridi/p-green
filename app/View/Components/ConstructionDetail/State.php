<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class State extends Component
{
    public $conststatus = null;
    public $alltechnisan = null;
    public $account = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($conststatus)
    {
        $this->conststatus = $conststatus['data'];
        $this->alltechnisan = $conststatus['tech'];
        $this->account = $conststatus['account'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.state');
    }
}
