<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class Materials extends Component
{
    public $matData = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($matData)
    {
        $this->matData = $matData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.materials');
    }
}
