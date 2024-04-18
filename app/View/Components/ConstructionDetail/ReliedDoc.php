<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReliedDoc extends Component
{
    public $relief = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($relief)
    {
        $this->relief = $relief;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.relied-doc');
    }
}
