<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class CustomerData extends Component
{
    public $cusdata = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($cusdata)
    {
        $this->cusdata = $cusdata;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.customer-data');
    }
}
