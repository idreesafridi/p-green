<?php

namespace App\View\Components\ConstructionStampa;

use Illuminate\View\Component;

class ccdl extends Component
{
    public $construction = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($construction)
    {
        $this->construction = $construction;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-stampa.ccdl');
    }
}
