<?php

namespace App\View\Components;

use App\Helper\BaseQuery;
use App\Models\ConstructionSite;
use Illuminate\View\Component;

class ConstructionDetailHead extends Component
{
    use BaseQuery;

    public $headdata = null;
    public $consId = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($consId = null )
    {   
        // dd($consId);
        if($consId != null){
           
           $this->consId = $consId;
        }else{
            $this->consId = $this->session_get("construction_id");
        }
        $this->headdata = $this->get_by_id(new ConstructionSite(), $consId);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail-head');
    }
}
