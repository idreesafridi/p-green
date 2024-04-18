<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class Papers extends Component
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
        return view('components.construction-detail.papers');
    }
}
