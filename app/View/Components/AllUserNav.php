<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AllUserNav extends Component
{
    public $userrole = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($userrole)
    {
        $this->userrole = $userrole;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.all-user-nav');
    }
}
