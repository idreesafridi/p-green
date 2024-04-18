<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class Assistances extends Component
{
    public $materialAssist = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($materialAssist)
    {
        $this->materialAssist = $materialAssist;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.assistances');
    }
}
