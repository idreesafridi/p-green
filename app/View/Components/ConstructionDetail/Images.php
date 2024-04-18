<?php

namespace App\View\Components\ConstructionDetail;

use Illuminate\View\Component;

class Images extends Component
{
    public $images_data = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($imagedata)
    {
        $this->images_data = $imagedata['data'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.images');
    }
}
