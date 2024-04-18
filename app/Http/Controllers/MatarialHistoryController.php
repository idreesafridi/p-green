<?php

namespace App\Http\Controllers;

use App\Models\MaterialList;
use Illuminate\Http\Request;
use App\Models\MatarialHistory;
use App\Models\ConstructionSite;
use App\Http\Controllers\Controller;
use App\Models\ConstructionMaterial;

class MatarialHistoryController extends Controller
{

    private $_request = null;
    private $_modal = null;


    public function __construct(Request $request, MatarialHistory $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
{
    $consId = $this->_request->input('data')[0]['construction_id'];

    // Initialize an array to store updated data
    
    $updatedData = [];
     foreach ($this->_request->all() as $key => $value) {
      
        foreach ($value as $k => $v) {
            $data = $this->get_by_id(new ConstructionMaterial(), $v['construction_material_id']);
            $originalName = $data->MaterialList->name;
        
            if ($data->material_list_id != $v['material_list_id']) {
                $updatedMaterial = MaterialList::find($v['material_list_id']);
                $updatedName = $updatedMaterial->name;

                $updatedData[] = [
                    'construction_site_id' => $data->construction_site_id,
                    'material_id' => $v['construction_material_id'],
                    'changeBy' => Auth()->id(),
                    'updated_field' => 'nome',
                    'Original' => $originalName,
                    'Updated_to' => $updatedName,
                    'reason' => 'Cambio configurazione'
                ];
            }
            if ($data->quantity != $v['quantity']) {
                $updatedData[] = [
                    'construction_site_id' => $data->construction_site_id,
                    'material_id' => $v['construction_material_id'],
                    'changeBy' => Auth()->id(),
                    'updated_field' => 'quantita',
                    'Original' => $data->quantity,
                    'Updated_to' => $v['quantity'],
                    'reason' => 'Cambio configurazione'
                ];
            }
            if(!empty($data->state) &&  !empty($v['state'])){
                if ( $data->state != $v['state']) {
                    $updatedData[] = [
                        'construction_site_id' => $data->construction_site_id,
                        'material_id' => $v['construction_material_id'],
                        'changeBy' => Auth()->id(),
                        'updated_field' => 'stato',
                        'Original' => $data->state,
                        'Updated_to' => $v['state'],
                        'reason' => 'aggiornamento'
                    ];
                }
            }
            elseif(empty($data->state) &&  (!empty($v['state']) && $v['state'] != 'Stato da selezionare')) {
                $updatedData[] = [
                    'construction_site_id' => $data->construction_site_id,
                    'material_id' => $v['construction_material_id'],
                    'changeBy' => Auth()->id(),
                    'updated_field' => 'stato',
                    'Original' => $data->state,
                    'Updated_to' => $v['state'],
                    'reason' => 'Cambio consegna'
                ];
            }
           
            // Add other conditions as needed for different fields
        }
    }


    // Render the view with the updated data and return as HTML
    $htmlContent = view('response.matarialHistory', ['updatedData' => $updatedData])->render();

    // Return the HTML content in the response
    return response()->json(['htmlContent' => $htmlContent]);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MatarialHistory  $matarialHistory
     * @return \Illuminate\Http\Response
     */
    public function show(MatarialHistory $matarialHistory)
    {
     
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MatarialHistory  $matarialHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(MatarialHistory $matarialHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MatarialHistory  $matarialHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MatarialHistory $matarialHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MatarialHistory  $matarialHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(MatarialHistory $matarialHistory)
    {
        //
    }

    public function store_material_history(){
        // dd($this->_request);
    }

    public function MaterialsHistories($id){
        $data = MatarialHistory::where('material_id', $id)->get();

        $htmlContent =   view('response.materialHistoryPrint', ['data' => $data])->render();

        return response()->json(['htmlContent' => $htmlContent]);
    }


    public function toggleMaterialHistory($id)
    {
       
         $status = $this->_request->input('data.status');
         $updated_field = $this->_request->input('data.state');
         $material_id = $this->_request->input('data.id');
      
         $Original = $status == '1' ? 'no' : 'si';
         $updatedName = $status == '1' ? 'si' : 'no';
       

        $updatedData = [
            'construction_site_id' => $id,
            'material_id' => $material_id,
            'changeBy' => Auth()->id(),
            'updated_field' => $updated_field,
            'Original' => $Original,
            'Updated_to' => $updatedName,
            'reason' => 'aggiornamento'
        ];

        $data =  view('response.materialToggel', ['updatedData' => $updatedData])->render();

        return response()->json(['htmlContent' => $data]);
    }
}
