<?php

namespace App\View\Components\ConstructionStampa;

use Illuminate\View\Component;

class crcee_le extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-stampa.crcee_le');
    }
}
