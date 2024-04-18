<?php

namespace App\View\Components;

use App\Models\MaterialOption;
use Illuminate\View\Component;

class MaterialTypeList extends Component
{
    public $materialTypeList = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->materialTypeList['Cappotto'] = MaterialOption::where('name', 'Cappotto')->where('status', 1)->first();
        $this->materialTypeList['Termico'] = MaterialOption::where('name', 'Termico')->where('status', 1)->first();
        $this->materialTypeList['Infissi'] = MaterialOption::where('name', 'Infissi')->where('status', 1)->first();
        $this->materialTypeList['Fotovoltaico'] = MaterialOption::where('name', 'Fotovoltaico')->where('status', 1)->first();
        $this->materialTypeList['Veicolo'] = MaterialOption::where('name', 'Veicolo')->where('status', 1)->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.material-type-list');
    }
}
