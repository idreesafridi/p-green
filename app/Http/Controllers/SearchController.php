<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PrNotDocFile;
use Illuminate\Http\Request;
use App\Models\ConstructionSite;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Models\ConstructionJobDetail;
use PhpParser\Node\Expr\AssignOp\Concat;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    private $_request = null;
    private $_model = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $model
     */
    public function __construct(Request $request, ConstructionSite $model)
    {
        $this->_request = $request;
        $this->_model = $model;
    }

    /**
     * Search assigned construction to auth user
     */
    public function construction_search_role()
    {
        // $pagename = $this->_request->pagename;
        // $search_keyword = $this->_request->keyword;

        $req = $this->_request->except('_token');
        //dd($req);
        $filter = [];

        if (array_key_exists('data', $req)) {
            foreach ($req['data'] as $key => $value) {
                $filter[$value['key']] = $value['value'];
            }
        } else {
            $req['data'] = [];
        }

        $cons = $this->_model->query();
        $pagename = $filter['pagename'];
        //$search_keyword = $filter['search_keyword'];
        if (array_key_exists('search_keyword', $filter)) {
            $search_keyword = $filter['search_keyword'];
        }
        else {
            $search_keyword = '';
        }
 
        if (auth()->user()->hasrole('technician')) {
            $cons->whereHas('StatusTechnician', function ($tecnico_q) {
                $tecnico_q->where('tecnician_id', auth()->id());
            });
        } else if (auth()->user()->hasrole('business')) {
            $cons->whereHas('ConstructionJobDetail', function ($business_q) {
                $business_q->where(function ($b_q) {
                    $b_q->orWhere('fixtures', auth()->id());
                    $b_q->orWhere('plumbing', auth()->id());
                    $b_q->orWhere('electrical', auth()->id());
                    $b_q->orWhere('construction', auth()->id());
                });
            });
        } else if (auth()->user()->hasrole('photovoltaic')) {
            $cons->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            });
        }

        $cons->where(function ($q) use ($search_keyword) {
            $q->orWhere('surename', 'LIKE', '%' . $search_keyword . '%');
            $q->orWhere('name', 'LIKE', '%' . $search_keyword . '%');

            $q->orWhereHas('PropertyData', function ($pro) use ($search_keyword) {
                $pro->where(function ($proq) use ($search_keyword) {
                    $proq->orWhere('property_common', 'LIKE', '%' . $search_keyword . '%');
                    $proq->orWhere('property_house_number', 'LIKE', '%' . $search_keyword . '%');
                    $proq->orWhere('property_street', 'LIKE', '%' . $search_keyword . '%');
                    $proq->orWhere('property_postal_code', 'LIKE', '%' . $search_keyword . '%');
                });
            });
        });
               

        $data = $this->searchPageName($cons, $pagename)->orderBy('surename', 'asc')->paginate(20);
        $result['result'] = view('response.construction-response', compact('data'))->render();

        $filter['pagename'] = $pagename;
        $result['count'] = $this->home_nav_count($data == null ? 0 : $data->count(), $filter['pagename']);

        if (auth()->user()->hasrole('business') && $filter['pagename'] != 'Active') {
            $result['result']= view('response.construction-response-null')->render();
        }

        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $req = $this->_request->except('_token');
        $filter = [];

        if (array_key_exists('data', $req)) {
            foreach ($req['data'] as $key => $value) {
                $filter[$value['key']] = $value['value'];
            }
        } else {
            $req['data'] = [];
        }


        $data = $this->search_queries($filter);
      
        $result['result'] = view('response.construction-response', compact('data'))->render();

        $filter['pagename'] = array_key_exists('pagename', $filter) ? $filter['pagename'] : 'Acive';
        $result['count'] = $this->home_nav_count($data == null ? 0 : $data->count(), $filter['pagename']);
       
        return response()->json($result);
    }

    /**
     * Get construction home nav count
     */
    private function home_nav_count($count, $pagename)
    {
        // if (auth()->user()->hasrole('technician')) {
        //     $data['active'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['close'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->where('status', 0)->count();

        //     $data['archived'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('archive', 1)->count();

        //     $data['external'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
        //         $q->where('type_of_construction', 1);
        //     })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['internal'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
        //         $q->where('type_of_construction', 0);
        //     })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['c50'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
        //         $q->where('type_of_deduction', 'like', '%50%');
        //     })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['c65'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
        //         $q->where('type_of_deduction', 'like', '%65%');
        //     })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['c90'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
        //         $q->where('type_of_deduction', 'like', '%90%');
        //     })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

        //     $data['condominio'] = $this->_model->whereHas('StatusTechnician', function ($tecnico_q) {
        //         $tecnico_q->where('tecnician_id', auth()->id());
        //     })->where('page_status', 4)
        //     ->where(function ($query) {
        //         $query->where(function ($subquery) {
        //             $subquery->whereHas('GetConstructionSiteCondomini')->where('status', 1)->where(function ($subsubquery) {
        //                 $subsubquery->where('archive', 0)->orWhereNull('archive');
        //             });
        //         })->orWhere(function ($subquery) {
        //             $subquery->whereHas('GetConstructionCondomini')->where('status', 1)->where(function ($subsubquery) {
        //                 $subsubquery->where('archive', 0)->orWhereNull('archive');
        //             });
        //         });
        //     })
        //     ->where(function ($query) {
        //         $query->where('archive', 0)->orWhereNull('archive');
        //     })
        //     ->count();
        // } 
        if (auth()->user()->hasrole('photovoltaic')) {
            $data['active'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['close'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->where('status', 0)->count();

            $data['archived'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('archive', 1)->count();

            $data['external'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_construction', 1);
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['internal'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_construction', 0);
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c50'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%50%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c65'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%65%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c90'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%90%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['condominio'] = $this->_model->whereHas('ConstructionJobDetail', function ($photovoltaic_q) {
                $photovoltaic_q->where('photovoltaic', auth()->id());
            })->where('page_status', 4)
            ->where(function ($query) {
                $query->where(function ($subquery) {
                    $subquery->whereHas('GetConstructionSiteCondomini')->where('status', 1)->where(function ($subsubquery) {
                        $subsubquery->where('archive', 0)->orWhereNull('archive');
                    });
                })->orWhere(function ($subquery) {
                    $subquery->whereHas('GetConstructionCondomini')->where('status', 1)->where(function ($subsubquery) {
                        $subsubquery->where('archive', 0)->orWhereNull('archive');
                    });
                });
            })
            ->where(function ($query) {
                $query->where('archive', 0)->orWhereNull('archive');
            })
            ->count();
        }
        elseif (auth()->user()->hasrole('business')) {

            $data['active'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['close'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['archived'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['external'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['internal'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c50'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c65'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c90'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['condominio'] = $this->_model->whereHas('ConstructionJobDetail', function ($q) {
                $q->Where('fixtures', auth()->id());
                $q->orWhere('plumbing', auth()->id());
                $q->orWhere('electrical', auth()->id());
                $q->orWhere('construction', auth()->id());
            })->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();
        }
        else {
            $data['active'] = $this->_model->where('page_status', 4)->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['close'] = $this->_model->where('page_status', 4)->where('status', 0)->count();
            $data['archived'] = $this->_model->where('page_status', 4)->where('status', 1)->where('archive', 1)->count();

            $data['external'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_construction', 1);
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['internal'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_construction', 0);
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c50'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%50%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c65'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%65%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            $data['c90'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
                $q->where('type_of_deduction', 'like', '%90%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            // $data['condominio'] = $this->_model->where('page_status', 4)->whereHas('ConstructionSiteSetting', function ($q) {
            //     $q->where('type_of_property', 'Condominio');
            // })->where('status', 1)->where('archive', 0)->orWhere('archive',null)->count();

            // $data['condominio'] = $this->_model
            // ->where('page_status', 4)
            // ->where(function ($query) {
            //     $query->where(function ($subquery) {
            //         $subquery->whereHas('GetConstructionSiteCondomini')->where('status', 1)->where(function ($subsubquery) {
            //             $subsubquery->where('archive', 0)->orWhereNull('archive');
            //         });
            //     })->orWhere(function ($subquery) {
            //         $subquery->whereHas('GetConstructionCondomini')->where('status', 1)->where(function ($subsubquery) {
            //             $subsubquery->where('archive', 0)->orWhereNull('archive');
            //         });
            //     });
            // })
            // ->where(function ($query) {
            //     $query->where('archive', 0)->orWhereNull('archive');
            // })
            // ->count();
            $data['condominio'] = $this->_model->where(function ($query) use ($pagename) {
                $query->whereHas('GetConstructionSiteCondomini', function ($q) use ($query) {
                    $query->whereHas('constructionSiteSetting', function ($innerQ) {
                        $innerQ->where('type_of_property', 'Condominio');
                    });
                    // Additional conditions for GetConstructionSiteCondomini relationship
                })->orWhereHas('GetConstructionCondominiOne', function ($q) use ($query) {
                    $q->whereHas('ConstructionSiteSettingforParent', function ($innerQ) {
                        $innerQ->where('type_of_property', 'Condominio');
                    });
                    $query->whereHas('constructionSiteSetting', function ($innerQ) {
                        $innerQ->where('type_of_property', '!=', 'Condominio');
                    });
                    // Additional conditions for GetConstructionCondominiOne relationship
                })->orWhereHas('constructionSiteSetting', function ($q) {
                    $q->where('type_of_property', 'Condominio');
                });
            })->where('status', 1)->where(function ($query) {
                $query->where('archive', 0)->orWhereNull('archive');
            })->count();


           
        }
    
        return $data;
    }

    /**
     * All search queries
     */
    private function search_queries($filter)
    {
        $pagename = $filter['pagename'];
        
        $cons = $this->_model->query();

        $cons->when(array_key_exists('search_keyword', $filter), function ($search_keyword_query) use ($filter) {
            $search_keyword = $filter['search_keyword'];

            $search_keyword_query->where(function ($query) use ($search_keyword) {
                $query->whereRaw("concat(surename, ' ', name) LIKE ?", ["%$search_keyword%"])
                    ->orWhereRaw("concat(name, ' ', surename) LIKE ?", ["%$search_keyword%"])
                    ->orWhere(function ($q) use ($search_keyword) {
                        $q->where('name', 'LIKE', '%' . $search_keyword . '%')
                            ->orWhere('surename', 'LIKE', '%' . $search_keyword . '%');
                            
                    })
                    ->orWhereHas('PropertyData', function ($pro) use ($search_keyword) {
                        $pro->where(function ($proq) use ($search_keyword) {
                            $proq->orWhere('property_common', 'LIKE', '%' . $search_keyword . '%');
                            $proq->orWhere('property_house_number', 'LIKE', '%' . $search_keyword . '%');
                            $proq->orWhere('property_street', 'LIKE', '%' . $search_keyword . '%');
                            $proq->orWhere('property_postal_code', 'LIKE', '%' . $search_keyword . '%');
                            //$proq->orWhereRaw("concat(property_street, ' ', property_house_number) LIKE ?", ["%$search_keyword%"]);
                            //$proq->orWhereRaw("concat(property_house_number, ' ', property_postal_code) LIKE ?", ["%$search_keyword%"]);
                            $proq->orWhereRaw("concat(property_street, ' ', property_house_number, ' ', property_postal_code) LIKE ?", ["%$search_keyword%"]);
                        });
                    });
            });
        }) 
        ->where('page_status', 4);
       
        $cons->when(array_key_exists('preanalysis', $filter), function ($preanalysis_query) use ($filter) {

            $preanalysis_arr = explode(',', $filter['preanalysis']); // convert preanalysis into array

            $preanalysis_query->whereHas('StatusPreAnalysis', function ($preanalysis_q) use ($preanalysis_arr) {
                $preanalysis_q->whereIn('state', $preanalysis_arr);
            });
        });

        $cons->when(array_key_exists('tecnico', $filter), function ($tecnico_query) use ($filter) {

            $tecnico_arr = explode(',', $filter['tecnico']); // convert tecnico into array

            if (in_array('Not Assigned', $tecnico_arr)) {
                array_push($tecnico_arr, '');
            }

            $tecnico_query->whereHas('StatusTechnician', function ($tecnico_q) use ($tecnico_arr) {
                $tecnico_q->whereIn('state', $tecnico_arr);
            });
        });

        $cons->when(array_key_exists('relaif', $filter), function ($relaif_query) use ($filter) {

            $relaif_arr = explode(',', $filter['relaif']); // convert relaif into array

            $relaif_query->whereHas('StatusRelief', function ($relaif_q) use ($relaif_arr) {
                $relaif_q->whereIn('state', $relaif_arr);
            });
        });

        $cons->when(array_key_exists('law_10', $filter), function ($law_10_query) use ($filter) {

            $law_10_arr = explode(',', $filter['law_10']); // convert law_10 into array

            $law_10_query->whereHas('StatusLegge10', function ($law_10_q) use ($law_10_arr) {
                $law_10_q->whereIn('state', $law_10_arr);
            });
        });

        $cons->when(array_key_exists('pre_noti', $filter), function ($pre_noti_query) use ($filter) {

            $pre_noti_arr = explode(',', $filter['pre_noti']); // convert pre_noti into array

            $pre_noti_query->whereHas('StatusPrNoti', function ($pre_noti_q) use ($pre_noti_arr) {
                $pre_noti_q->whereIn('state', $pre_noti_arr);
            });
        });

        $cons->when(array_key_exists('register_practice', $filter), function ($register_practice_query) use ($filter) {

            $register_practice_arr = explode(',', $filter['register_practice']); // convert register_practice into array

            $register_practice_query->whereHas('statusRegPrac', function ($register_practice_q) use ($register_practice_arr) {
                $register_practice_q->whereIn('state', $register_practice_arr);
            });
        });

        $cons->when(array_key_exists('work_started', $filter), function ($work_started_query) use ($filter) {

            $work_started_arr = explode(',', $filter['work_started']); // convert work_started into array

            $work_started_query->whereHas('StatusWorkStarted', function ($work_started_q) use ($work_started_arr) {
                $work_started_q->whereIn('state', $work_started_arr);
            });
        });

        $cons->when(array_key_exists('sal', $filter), function ($sal_query) use ($filter) {

            $sal_arr = explode(',', $filter['sal']); // convert sal into array

            $sal_query->whereHas('StatusSAL', function ($sal_q) use ($sal_arr) {
                $sal_q->whereIn('state', $sal_arr);
            });
        });

        $cons->when(array_key_exists('balance_enea', $filter), function ($balance_enea_query) use ($filter) {

            $balance_enea_arr = explode(',', $filter['balance_enea']); // convert balance_enea into array

            $balance_enea_query->whereHas('StatusEneaBalance', function ($balance_enea_q) use ($balance_enea_arr) {
                $balance_enea_q->whereIn('state', $balance_enea_arr);
            });
        });

        $cons->when(array_key_exists('locked_down', $filter), function ($locked_down_query) use ($filter) {

            $locked_down_arr = explode(',', $filter['locked_down']); // convert locked_down into array

            $locked_down_query->whereHas('StatusWorkClose', function ($locked_down_q) use ($locked_down_arr) {
                $locked_down_q->whereIn('state', $locked_down_arr);
            });
        });

        $cons->when(array_key_exists('computo', $filter), function ($computo_query) use ($filter) {

            $computo_arr = explode(',', $filter['computo']); // convert computo into array

            $computo_query->whereHas('StatusComputation', function ($computo_q) use ($computo_arr) {
                $computo_q->whereIn('state', $computo_arr);
            });
        });

         $search_keyword = array_key_exists('search_keyword', $filter);
          
        if (!empty($search_keyword)) {
            $query = $this->searchPageName($cons, $pagename, $search_keyword);
            $records = $query->paginate(20);
        
            $mergedData = collect([]);
        
            foreach ($records as $record) {
                // Fetch all ConstructionCondomini related to this record
                $allConstructionCondomini = $record->GetConstructionSiteCondominies->pluck('ConstructionCondomini')->flatten();
        
                // Fetch the parent ConstructionSite record
                $parentRecord = $record; // Change this to whatever you need to fetch the parent record
        
                // Ensure $allConstructionCondomini is a collection before merging
                if ($allConstructionCondomini instanceof \Illuminate\Support\Collection) {
                    // Merge the child records
                    $mergedData = $mergedData->merge($allConstructionCondomini);
                } else {
                    // If $allConstructionCondomini is not a collection, assume it's a single model instance
                    $mergedData->push($allConstructionCondomini);
                }
        
                // Ensure $parentRecord is a model instance before merging
                if ($parentRecord instanceof \Illuminate\Database\Eloquent\Model) {
                    // Merge the parent record
                    $mergedData->push($parentRecord);
                }
            }
           
          
            // Create a new paginator from the merged records
            // $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            //     $mergedData->forPage($records->currentPage(), $records->perPage()),
            //     $mergedData->count(),
            //     $records->perPage(),
            //     $records->currentPage(),
            //     ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            // );

            // dd($mergedData, $paginator->items());
        
            // Manually filter out duplicates from paginator's items
            $uniqueItems = collect([]);
            // dd($paginator->items());
            foreach ($mergedData as $item) {
              
                if (!empty($item->id) && !$uniqueItems->contains('id', $item->id)) {
                    $uniqueItems->push($item);
                }
            }

            // dd($uniqueItems, $mergedData);
        
            $totalItems = $uniqueItems->count(); // Total count of items
            $perPage = $records->perPage(); // Number of items per page
            $currentPage = $records->currentPage(); // Current page
            
            // Calculate the starting index for the slice
            $startingIndex = ($currentPage - 1) * $perPage;
            
            // Slice the collection to get only the items for the current page
            $itemsForCurrentPage = $uniqueItems->slice($startingIndex, $perPage);
            
            // Create a new paginator manually
            $uniquePaginator = new LengthAwarePaginator(
                $itemsForCurrentPage, // Items for the current page
                $totalItems, // Total count of items
                $perPage, // Number of items per page
                $currentPage, // Current page
                ['path' => Paginator::resolveCurrentPath()] // Path for the paginator
            );
            
            return $uniquePaginator;
        } else {
           return $this->searchPageName($cons, $pagename, $search_keyword)->orderBy('surename', 'asc')->paginate(20);
        }
         
        // return $this->searchPageName($cons, $pagename, $search_keyword)->orderBy('surename', 'asc')->paginate(20);
    }

    private function searchPageName($cons, $pagename, $search_keyword = null)
    {
   
        if ($pagename == 'Active') {

            $cons->where('status', 1)->where('archive', 0)->orWhere('archive',null);
        } else if ($pagename == 'Closed') {
            
            $cons->where('status', 0);
        } else if ($pagename == 'Archived') {

            $cons->where('status', 1)->where('archive', 1);
        } else if ($pagename == 'Interior' || $pagename == 'Outdoor') {

            if ($pagename == 'Interior') {
                $pagename = 0;
            } else {
                $pagename = 1;
            }

            $cons->whereHas('ConstructionSiteSetting', function ($q) use ($pagename) {
                $q->where('type_of_construction', $pagename);
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null);
        } else if ($pagename == '50' || $pagename == '65' || $pagename == '90') {

            $cons->whereHas('ConstructionSiteSetting', function ($q) use ($pagename) {
                $q->where('type_of_deduction', 'like', '%' . $pagename . '%');
            })->where('status', 1)->where('archive', 0)->orWhere('archive',null);
        }
        //  else if ($pagename == 'Condominiums' && $search_keyword != true) {
        //     $cons->where('page_status', 4)
        //     ->where(function ($query) {
        //         $query->whereHas('GetConstructionSiteCondomini')
        //             ->where('status', 1)
        //             ->where(function ($subquery) {
        //                 $subquery->where('archive', 0)
        //                     ->orWhereNull('archive');
        //             });
        //     })
        //     ->orWhere(function ($query) {
        //         $query->whereHas('GetConstructionCondomini')
        //             ->where('status', 1)
        //             ->where(function ($subquery) {
        //                 $subquery->where('archive', 0)
        //                     ->orWhereNull('archive');
        //             });
        //     });
          
        
        
        // }
        else if($pagename == 'Condominiums'){   
            
            // $cons->where(function ($query) use ($pagename) {
            //     $query->whereHas('GetConstructionSiteCondomini', function ($q) use ($query) {
            //         $query->constructionSiteSetting->where('type_of_property', 'Condominio');
            //         // Additional conditions for GetConstructionSiteCondomini relationship
            //     })->orWhereHas('GetConstructionCondominiOne', function ($q) use ($query) {
            //         $query->constructionSiteSetting->where('type_of_property', '!=', 'Condominio');
            //         // Additional conditions for GetConstructionCondominiOne relationship
            //     })->orWhereHas('constructionSiteSetting', function ($q) {
            //         $q->where('type_of_property', 'Condominio');
            //     });
            // })->where('status', 1)->where(function ($query) {
            //     $query->where('archive', 0)->orWhereNull('archive');
            // });
            $cons->where(function ($query) use ($pagename) {
                $query->whereHas('GetConstructionSiteCondomini', function ($q) use ($query) {
                    $query->whereHas('constructionSiteSetting', function ($innerQ) {
                        $innerQ->where('type_of_property', 'Condominio');
                    });
                    // Additional conditions for GetConstructionSiteCondomini relationship
                })->orWhereHas('GetConstructionCondominiOne', function ($q) use ($query) {
                    
                    $q->whereHas('ConstructionSiteSettingforParent', function ($innerQ) {
                        $innerQ->where('type_of_property', 'Condominio');
                    });
                    $query->whereHas('constructionSiteSetting', function ($innerQ) {
                        $innerQ->where('type_of_property', '!=', 'Condominio');
                    });
                    // Additional conditions for GetConstructionCondominiOne relationship
                })->orWhereHas('constructionSiteSetting', function ($q) {
                    $q->where('type_of_property', 'Condominio');
                });
            })->where('status', 1)->where(function ($query) {
                $query->where('archive', 0)->orWhereNull('archive');
            });
            
            
        
        }

        $cons->where('page_status', 4)->orderByRaw("CASE
                WHEN surename LIKE 'A%' OR surename LIKE 'a%' THEN 1
                WHEN surename LIKE 'B%' OR surename LIKE 'b%' THEN 2
                WHEN surename LIKE 'C%' OR surename LIKE 'c%' THEN 3
                WHEN surename LIKE 'D%' OR surename LIKE 'd%' THEN 4
                WHEN surename LIKE 'E%' OR surename LIKE 'e%' THEN 5
                WHEN surename LIKE 'F%' OR surename LIKE 'f%' THEN 6
                WHEN surename LIKE 'G%' OR surename LIKE 'g%' THEN 7
                WHEN surename LIKE 'H%' OR surename LIKE 'h%' THEN 8
                WHEN surename LIKE 'I%' OR surename LIKE 'i%' THEN 9
                WHEN surename LIKE 'J%' OR surename LIKE 'j%' THEN 10
                WHEN surename LIKE 'K%' OR surename LIKE 'k%' THEN 11
                WHEN surename LIKE 'L%' OR surename LIKE 'l%' THEN 12
                WHEN surename LIKE 'M%' OR surename LIKE 'm%' THEN 13
                WHEN surename LIKE 'N%' OR surename LIKE 'n%' THEN 14
                WHEN surename LIKE 'O%' OR surename LIKE 'o%' THEN 15
                WHEN surename LIKE 'P%' OR surename LIKE 'p%' THEN 16
                WHEN surename LIKE 'Q%' OR surename LIKE 'q%' THEN 17
                WHEN surename LIKE 'R%' OR surename LIKE 'r%' THEN 18
                WHEN surename LIKE 'S%' OR surename LIKE 's%' THEN 19
                WHEN surename LIKE 'T%' OR surename LIKE 't%' THEN 20
                WHEN surename LIKE 'U%' OR surename LIKE 'u%' THEN 21
                WHEN surename LIKE 'V%' OR surename LIKE 'v%' THEN 22
                WHEN surename LIKE 'W%' OR surename LIKE 'w%' THEN 23
                WHEN surename LIKE 'X%' OR surename LIKE 'x%' THEN 24
                WHEN surename LIKE 'Y%' OR surename LIKE 'y%' THEN 25
                WHEN surename LIKE 'Z%' OR surename LIKE 'z%' THEN 26
                WHEN surename IS NULL THEN 27
                ELSE 28
            END");

        return $cons;
    }

    public function centri_search()
    {
        $search_keyword = $this->_request->search;
        $cons = $this->_model->query();

        if (auth()->check() && auth()->user()->hasRole('technician')) {
            $cons->whereHas('StatusTechnician', function ($tecnico_q) {
                $tecnico_q->where('technician_id', auth()->id());
            });
        }

        $searchKeyword = '%' . $search_keyword . '%';

        $data = $cons->where(function ($query) use ($searchKeyword) {
            $query->whereRaw("CONCAT(surename, ' ', name) LIKE ?", $searchKeyword)
                ->orWhereRaw("CONCAT(name, ' ', surename) LIKE ?", $searchKeyword)
                ->orWhere('name', 'LIKE', $searchKeyword)
                ->orWhere('surename', 'LIKE', $searchKeyword)
                ->orWhere('latest_status', 'LIKE', $searchKeyword);
        })->orWhereHas('PropertyData', function ($pro) use ($searchKeyword) {
            $pro->where(function ($proq) use ($searchKeyword) {
                $proq->orWhere('property_common', 'LIKE', $searchKeyword);
                $proq->orWhere('property_house_number', 'LIKE', $searchKeyword);
                $proq->orWhere('property_street', 'LIKE', $searchKeyword);
                $proq->orWhere('property_postal_code', 'LIKE', $searchKeyword);
            });
        })->where('page_status', 4)->get();

        $result = array();

        foreach ($data as $value) {
            $result[] = array("id" => $value->id, "text" => $value->name . ' ' . $value->surename);
        }

        return response()->json(['items' => $result, 'total_count' => count($result)]);
    }

    public function get_model_column()
    {
        $class = $this->_request->data;

        switch ($class) {
            case 'Property Data':
                $arr = [
                    'property_street',
                    'property_house_number',
                    'property_postal_code',
                    'property_common',
                    'property_province',
                    'cadastral_dati',
                    'cadastral_section',
                    'cadastral_category',
                    'cadastral_particle',
                    'sub_ordinate',
                    'pod_code',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Documents And Contact':
                $arr = [
                    'document_number',
                    'issued_by',
                    'release_date',
                    'expiration_date',
                    'fiscal_document_number',
                    'vat_number',
                    'contact_email',
                    'contact_number',
                    'alt_refrence_name',
                    'alt_contact_number'
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Construction Materials':
                $arr = [
                    'material_list_id',
                    'quantity',
                    'state',
                    'consegnato',
                    'montato',
                    'updated_by',
                    'avvio',
                    'note',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Construction Site Setting':
                $arr = [
                    'type_of_property',
                    'type_of_construction',
                    'type_of_deduction',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Computo':
                $arr = [
                    'state',
                    'updated_on',
                    'updated_by',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Enea Balance':
                $arr = [
                    'state',
                    'select_accountant',
                    'updated_on',
                    'updated_by',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Legge 10':
                $arr = [
                    'state',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Pre Analysis':
                $arr = [
                    'state',
                    'turnover',
                    'embedded',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days'
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'PrNoti':
                $arr = [
                    'state',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'RegPrac':
                $arr = [
                    'state',
                    'file_name',
                    'file_path',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Relief':
                $arr = [
                    'state',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days'
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'SAL':
                $arr = [
                    'state',
                    'select_accountant',
                    'updated_on',
                    'updated_by',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Technician':
                $arr = [
                    'tecnician_id',
                    'state',
                    'updated_on',
                    'updated_by',
                    'reminders_emails',
                    'reminders_days'
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Work Close':
                $arr = [
                    'state',
                    'updated_on',
                    'updated_by',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Work Started':
                $arr = [
                    'state',
                    'work_started_date',
                    'updated_on',
                    'updated_by',
                    'status',
                ];

                $result = view('response.model_column_list', compact('arr'))->render();
                break;
            case 'Construction Job Detail':
                $result = view('response.construction_job_details_column_list')->render();
                break;
            case 'Contratto 110':
                $arr = [
                    'assigned',
                    'missing',
                ];
                $result = view('response.construction_job_details_column_list', compact('arr'))->render();
                break;
            default:
                $result = null;
                break;
        }

        return response()->json(['result' => $result]);
    }

    public function generateReport()
    {
        $data = $this->_request->except('_token');
        //dd($this->_request->all());
        $filter = [];

        if (isset($data['model_column'])) {
            $columnName = $data['model_column'];

            $cons = $this->_model->query();
            
            $this->session_store('column', $columnName);
            $modelName = $this->setModelName($data['model_list']);

            if (in_array('archive', $columnName)) {
                $cons->where('archive', 0);
                $columnName = array_diff($columnName, ['archive']);
            }

            if (in_array('closed', $columnName)) {
                $cons->whereHas('StatusWorkClose', function ($work) {
                    $work->where(function ($subquery) {
                        $subquery->whereNull('state')
                            ->orWhere('state', 'waiting');
                    });
                });

                $columnName = array_diff($columnName, ['closed']);
            }

            $conData = $cons->where('page_status', 4)->orderBy('id')->cursorPaginate(10);

        } else {
            $conData = null;
            $modelName = null;
            $columnName = null;
        } 

        if ($data['model_list'] == 'Contratto 110') {
            $construction = PrNotDocFile::where('file_name', 'Contratto 110')->orderBy('id')->cursorPaginate(10);
            $conData = [];

            if (isset($data['model_column'])) {
                if (in_array('assigned', $columnName)) {
                    foreach ($construction as $constructionRecord) {
                        $con = $this->_model->where('id', $constructionRecord->construction_site_id)->first();
                        $conData[] = $con;
                    }
                }
                else {
                    $constructionIds = $construction->pluck('construction_site_id')->toArray();
                    $conData = ConstructionSite::whereNotIn('id', $constructionIds)->where('page_status', 4)->orderBy('id')->cursorPaginate(10);
                }        
            }
        }


        if($data['model_list'] == 'Construction Job Detail') {
            $filter['tech'] = $this->user_by_role(new User(), 'technician');
            $filter['account'] = $this->user_by_role(new User(), 'businessconsultant');
            $filter['photovoltaic'] = $this->user_by_role(new User(), 'photovoltaic');
            $filter['plumbing'] = $this->business_user_by_role(new User(), 'Idraulico');
            $filter['fixtures'] = $this->business_user_by_role(new User(), 'Infissi');
            $filter['electrician'] = $this->business_user_by_role(new User(), 'Elettricista');
            $filter['construction'] = $this->business_user_by_role(new User(), 'Edile');

            $filters = view('response.report_list_filters', compact('modelName', 'columnName', 'filter'))->render();
        }
        else {
            $filters = view('response.report_list_filters', compact('modelName', 'columnName'))->render();
        }
        $result = view('response.report_list', compact('columnName', 'conData', 'modelName', 'filter'))->render();

        return response()->json(['result' => $result, 'filters' => $filters]);
    }

    public function reportsSearch()
    {
        $req = $this->_request->except('_token');
        $filter = [];

        if (array_key_exists('data', $req)) {
            foreach ($req['data'] as $key => $value) {
                $filter[$value['key']] = $value['value'];
            }
        } else {
            $req['data'] = [];
        }

        $modelName = $filter['model'];
        unset($filter['model']);

        $columnName = $this->session_get('column');
        $conData = $this->_model->get();

        $result = view('response.report_list', compact('columnName', 'conData', 'modelName', 'filter'))->render();
        return response()->json(['result' => $result]);
    }

    public function get_job_reports()
    {
        $data = $this->_request->except('_token');
        //dd($this->_request->all());
        $columnName = $this->_request->columnName;
        $id = $this->_request->id;
        $modelName = 'ConstructionJobDetail';

        $construction = ConstructionJobDetail::where($columnName, $id)->get();

        $conData = [];
        foreach ($construction as $constructionRecord) {
            $con = $this->_model->where('id', $constructionRecord->construction_site_id)->first();

            // Append the result to the $conData array
            $conData[] = $con;
        }

        $columnName = (array) $columnName;

        $result = view('response.report_list', compact('columnName', 'conData', 'modelName'))->render();
        return response()->json(['result' => $result]);
    }

    private function setModelName($modelname)
    {
        switch ($modelname) {
            case 'Property Data':
                $modelName = 'PropertyData';
                break;
            case 'Documents And Contact':
                $modelName = 'DocumentAndContact';
                break;
            case 'Construction Materials':
                $modelName = 'ConstructionMaterial';
                break;
            case 'Construction Site Setting':
                $modelName = 'ConstructionSiteSetting';
                break;
            case 'Computo':
                $modelName = 'StatusComputation';
                break;
            case 'Enea Balance':
                $modelName = 'StatusEneaBalance';
                break;
            case 'Legge 10':
                $modelName = 'StatusLegge10';
                break;
            case 'Pre Analysis':
                $modelName = 'StatusPreAnalysis';
                break;
            case 'PrNoti':
                $modelName = 'StatusPrNoti';
                break;
            case 'RegPrac':
                $modelName = 'statusRegPrac';
                break;
            case 'Relief':
                $modelName = 'StatusRelief';
                break;
            case 'SAL':
                $modelName = 'StatusSAL';
                break;
            case 'Technician':
                $modelName = 'StatusTechnician';
                break;
            case 'Work Close':
                $modelName = 'StatusWorkClose';
                break;
            case 'Work Started':
                $modelName = 'StatusWorkStarted';
                break;
            case 'Construction Job Detail':
                $modelName = 'ConstructionJobDetail';
                break;
            case 'Contratto 110':
                $modelName = 'PrNotDocFile';
                break;
            default:
                $modelName = null;
                break;
        }

        return $modelName;
    }
}
