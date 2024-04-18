<?php

namespace App\View\Components;

use App\Helper\BaseQuery;
use App\Models\ConstructionSite;
use App\Models\ConstructionSiteImage;
use Illuminate\View\Component;

class ConstructionDetailNav extends Component
{
    use BaseQuery;

    public $constructionid = null;
    public $conststatus = null;
    public $imagecount = null;
   


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($constructionid = null)
    {
        // dd($constructionid);  
        if($constructionid != null)
        {
            $this->constructionid = $constructionid;
        }
        else
        {
            $this->constructionid = $this->session_get("construction_id");
        }
      
        $this->conststatus = ConstructionSite::find($this->constructionid);

        $this->imagecount['ante'] = ConstructionSiteImage::where('folder', 'ante')->where('construction_site_id', $this->constructionid)->where('status', 1)->count();
        $this->imagecount['durante'] = ConstructionSiteImage::where('folder', 'durante')->where('construction_site_id', $this->constructionid)->where('status', 1)->count();
        $this->imagecount['post'] = ConstructionSiteImage::where('folder', 'post')->where('construction_site_id', $this->constructionid)->where('status', 1)->count();
        $this->imagecount['cantiere'] = ConstructionSiteImage::where('folder', 'cantiere')->where('construction_site_id', $this->constructionid)->where('status', 1)->count();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.construction-detail-nav');
    }
}
