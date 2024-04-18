<?php

namespace App\View\Components\ConstructionDetail;

use App\Models\ConstructionCondomini;
use App\Models\ConstructionSite;
use Illuminate\View\Component;

class BuildingSite extends Component
{
    public $builddata = null;
    public $photovoltaic = null;
    public $plumbing = null;
    public $fixtures = null;
    public $electrician = null;
    public $construction = null;
    public $tech = null;
    public $condoList = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($builddata)
    {
        $this->builddata = $builddata['data'];
        $this->tech = $builddata['tech'];
        $this->photovoltaic = $builddata['photovoltaic'];
        $this->plumbing = $builddata['plumbing'];
        $this->fixtures = $builddata['fixtures'];
        $this->electrician = $builddata['electrician'];
        $this->construction = $builddata['construction'];

        // $getCondo = ConstructionCondomini::whereNotNull('construction_site_id')->pluck('construction_site_id')->toArray();
        $getCondoChild = ConstructionCondomini::whereNotNull('construction_assigned_id')->pluck('construction_assigned_id')->toArray();

        // array_push($getCondo, $this->builddata->id);
        // $this->condoList = ConstructionSite::get();
        // $this->condoList = ConstructionSite::whereNotIn('id', $getCondo)->whereNotIn('id', $getCondoChild)->get();

        // $cons = ConstructionSite::get();

        // $this->condoList =     ConstructionSite::where(function ($query) {
        //     $query->orWhereHas('GetConstructionSiteCondomini', function ($q) use ($query) {
        //         $query->whereHas('constructionSiteSetting', function ($innerQ) {
        //             $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //         });

                
        //         // Additional conditions for GetConstructionSiteCondomini relationship
        //     })->orWhereHas('GetConstructionCondominiOne', function ($q) use ($query) {
        //         $query->whereHas('constructionSiteSetting', function ($innerQ) {
        //             $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //         });
        //         $q->whereHas('ConstructionSiteSettingforParent', function ($innerQ) {
        //             $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //         });
        //     })->orWhereHas('constructionSiteSetting', function ($q) {
        //         $q->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //     });
        // })->where('status', 1)->where(function ($query) {
        //     $query->where('archive', 0)->orWhereNull('archive');
        // })->count();

        // dd($this->condoList);



        //    dd($cons );



        // $this->condoList = ConstructionSite::where('status', 1)
        // ->where('page_status', 4)
        // ->where(function ($query) {
        //     $query->where('archive', 0)->orWhereNull('archive');
        // })->WhereHas('constructionSiteSetting', function ($q) {
        //     $q->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        // })
        // ->orWhere(function ($query) {
        //     $query->WhereHas('GetConstructionCondominiOne', function ($q) {
        //         $q->whereHas('ConstructionSiteSettingforParent', function ($innerQ) {
        //             $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //         });
        //     })->WhereHas('GetConstructionSiteCondomini', function ($q) use ($query) {
        //         $query->whereHas('constructionSiteSetting', function ($innerQ) {
        //             $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
        //         });
        //     });
        // })->get();

        $data = ConstructionSite::where('status', 1)
        ->where('page_status', 4)
        ->where(function ($query) {
            $query->where('archive', 0)->orWhereNull('archive');
        })->get ();

        $first = ConstructionSite::where('status', 1)
    ->where('page_status', 4)
    ->where(function ($query) {
        $query->where('archive', 0)->orWhereNull('archive');
    })->Where(function ($query) {
        $query ->orWhereHas('GetConstructionSiteCondomini', function ($q)  use ($query){
            $query->whereHas('constructionSiteSetting', function ($innerQ) {
                $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
            });
        });
     
        
    })
  
    ->where(function ($query) {
        $query ->orWhereHas('GetConstructionCondominiOne', function ($q) {
            $q->whereHas('ConstructionSiteSettingforParent', function ($innerQ) {
                $innerQ->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
            });
          
        });
    })
    // ->where(function ($query) {
    //     $query ->orWhereHas('constructionSiteSetting', function ($q) {
    //         $q->where('type_of_property', '!=', ucwords(strtolower('Condominio')));
            
    //     });
    // })
   
    ->get();


    // dd( $this->condoList);
      
         


        $second = $data->filter(function ($constructionSite) use ($getCondoChild) {
            return $constructionSite->ConstructionSiteSetting->type_of_property != "Condominio"
                && !in_array($constructionSite->id, $getCondoChild);
        });

        $this->condoList = ($first)->merge($second);


    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail.building-site');
    }
}
