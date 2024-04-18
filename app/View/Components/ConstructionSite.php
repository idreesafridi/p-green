<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ConstructionSite extends Component
{
    public $constructionData;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($constructionData = null)
    {
        //
        $this->constructionData = $constructionData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-site');
    }
}
