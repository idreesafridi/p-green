<?php

namespace App\View\Components\ConstructionDetail;

use App\Models\ConstructionNotes;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class Note extends Component
{
    public $notes = null;
    public $cons = null;
    

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($cons = null)
    {   

        if($cons != null){
            $this->cons = $cons;
        }else{
            $cons = Session::get('construction_id');
        }
        $this->notes = ConstructionNotes::orderby('priority', 'DESC')->where('construction_site_id', $cons)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.note');
    }
}
