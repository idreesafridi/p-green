<?php

namespace App\View\Components\Front;

use Illuminate\View\Component;

class Links extends Component
{
    public $pageTitle;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($pageTitle = null)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.front.links');
    }
}
