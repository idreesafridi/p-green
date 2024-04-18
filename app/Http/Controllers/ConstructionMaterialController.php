<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\MaterialList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessDetail;
use App\Models\ConstructionMaterial;
use App\Models\MatarialHistory;
use App\Models\MaterialPrice;

class ConstructionMaterialController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = 'construction.construction-material.';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionMaterial $modal)
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
        $data['all'] = $this->get_all($this->_modal);
        return view($this->_directory . 'all', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->_directory . 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($consId = null)
    {

        $consId =   $consId != null ? $consId : $this->session_get('construction_id');

        $data = $this->_request->except('_token');
        $data['updated_by'] = auth()->id();

        $data['construction_site_id'] = $consId;
        $save =  $this->add($this->_modal, $data);
  
        $materialHistoryData = [
            'construction_site_id' => $consId,
            'material_id' => $save->id,
            'changeBy' => Auth()->id(),
            'updated_field' => 'nome',
            'Original' => null,
            'Updated_to' => $save->MaterialList->name,
            'reason' => 'Materiale aggiunto',
        ];
            
        MatarialHistory::create($materialHistoryData);

        return redirect()->route('construction_detail', ['id' => $data['construction_site_id'], 'pagename' => 'Materiali'])->with('success', 'Materiale aggiunto con successo.');
    }

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view($this->_directory . 'show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        return view($this->_directory . 'edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \{{ namespacedModel }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
      
        $data = $this->_request->except('_token', 'old_material_list_id', 'old_quantity', 'old_material_list_id_for_all', 'old_quantity_for_all','consegnato', 'montato','construction_site_id_history', 'material_id_history', 'changeBy_history', 'updatedField_history', 'Original_history', 'Updated_to_history','reason');
        // $data = $this->_request->except('_token');
       
        $historyData  = $this->_request->only('construction_site_id_history', 'material_id_history', 'changeBy_history', 'updatedField_history', 'Original_history', 'Updated_to_history','reason');
      
       

        $old_data = $this->_request->only('old_material_list_id', 'old_quantity');
        $old_data_for_all = $this->_request->only('old_material_list_id_for_all', 'old_quantity_for_all');


        $construct_id = $id != null ? $id : $this->session_get("construction_id");

        if (array_key_exists('construction_material_id', $data)) {

            $updated_data =  [];
            // foreach ($data['construction_material_id'] as $i => $matD) {
            for ($i = 0; $i < count($data['construction_material_id']); $i++) {
                if ($data['change'][$i] == 1) {
                    $id = array_key_exists($i, $data['construction_material_id']) ? $data['construction_material_id'][$i] : null;
                    //dd($data);
                    // dd($i, $data['quantity']);
                    $loopArr = [
                        'material_list_id' => array_key_exists($i, $data['material_list_id']) ? $data['material_list_id'][$i] : null,
                        'quantity' => array_key_exists($i, $data['quantity']) ? $data['quantity'][$i] : null,
                        'state' => array_key_exists($i, $data['state']) ? $data['state'][$i] : null,
                        'avvio' => array_key_exists($i, $data['avvio']) ? $data['avvio'][$i] : null,
                        'note' => array_key_exists($i, $data['note']) ? $data['note'][$i] : null,
                        'updated_by' => auth()->id()
                    ];

                    $update_mat = $this->get_by_id($this->_modal, $id);
                
                    if ($update_mat->material_list_id != $loopArr['material_list_id'] || $update_mat->quantity != $loopArr['quantity']) {

                        $oldMaterialOptionName =  $update_mat->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                        
                        $new_name =  $this->get_by_id(new MaterialList(), $loopArr['material_list_id']);
                        $new_name_matrial =  $new_name->name;
                        $old_name =  $update_mat->MaterialList->name;


                        $updated_data[] = [
                            'material_option_name' => $oldMaterialOptionName,
                            'new_material_name' => $new_name_matrial,
                            'old_material_name' => $old_name,
                            'quantity' => $loopArr['quantity'],
                            'old_quantity' => $update_mat->quantity
                        ];
                    }


                    $update_mat->update($loopArr);

                    if (array_key_exists($i, $data['avvio'])) {
                        if ($data['avvio'][$i] != null) {
                            // store construction id in session

                            $assitance['construction_site_id'] = $construct_id;

                            $assitance['machine_model'] = $update_mat->MaterialList->name;
                            $assitance['state'] = "Da completare";
                            $assitance['start_date'] = array_key_exists($i, $data['avvio']) ? $data['avvio'][$i] : null;
                            $assitance['expiry_date'] = array_key_exists($i, $data['avvio']) ? Carbon::parse($data['avvio'][$i])->addYears(1) : null;
                            $assitance['updated_by'] = Auth()->user()->name;
                            // update material assistanse

                            $year = \Carbon\Carbon::parse($assitance['expiry_date'])->format('Y');

                            $folder_name = $assitance['machine_model'] . ' ' . $year;


                            $this->createAssistanceFolder($folder_name, $construct_id);


                            $update_mat->MaterialsAsisstance()->create($assitance);

                            // $folder_name = $assitance['machine_model'] . '_' . $assitance['start_date'];
                            // $this->createAssistanceFolder($folder_name, $construct_id);
                        }
                    }

                    $name = $update_mat->ConstructionSite->name . ' ' . $update_mat->ConstructionSite->surename;

                    if ($update_mat->MaterialList != null) {
                        if ($update_mat->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name == 'Fotovoltaico' && $update_mat->consegnato == 1) {

                            $to = 'gabriele.greengen@gmail.com';
                            $subject = 'CONSEGNATO INVERTER || Cantiere ' . $name;

                            $this->materialMail($to, $subject, $name, 'Materiale è stato consegnato al cantiere ' . $name . ' ma risulta ancora da bollettare');
                        }
                    }

                    if ($update_mat->consegnato == 1 && $update_mat->state == 'Da bollettare') {
                        $to = 'segreteria.greengen@gmail.com';
                        $subject = 'CONSEGNATO || Cantiere ' . $name;
                        $this->materialMail($to, $subject, $name, 'Materiale è stato consegnato al cantiere ' . $name . ' ma risulta ancora da bollettare');
                    }
                }
            }

            if (isset($old_data['old_material_list_id'], $data['material_list_id'])) {

                $intersection = array_intersect($data['material_list_id'], $old_data['old_material_list_id']);

                $missingValues = array_diff($old_data['old_material_list_id'], $intersection);
            }
            if (isset($old_data['old_quantity'], $data['quantity'])) {

                $intersection = array_intersect($data['quantity'], $old_data['old_quantity']);

                $quantitymissingValues = array_diff($old_data['old_quantity'], $intersection);
            }

            if (isset($missingValues) && !empty($missingValues) || isset($quantitymissingValues) && !empty($quantitymissingValues)) {

                $data  = $updated_data;
                $name = $this->get_by_id($this->_modal, $id)->ConstructionSite->name . ' ' . $this->get_by_id($this->_modal, $id)->ConstructionSite->surename;
                $to = 'fotovoltaico.greengen@gmail.com';
                $subject = 'Cambio configurazione Materiali';
                $this->materialChangeMail($to, $subject, $name, $data, $construct_id);
            }


            // if (isset($old_data_for_all['old_material_list_id_for_all'], $data['material_list_id'])) {

            //     $intersectionForall = array_intersect($data['material_list_id'], $old_data_for_all['old_material_list_id_for_all']);

            //     $missingValuesforAll = array_diff($old_data_for_all['old_material_list_id_for_all'], $intersectionForall);
            // }
            // if (isset($old_data_for_all['old_quantity_for_all'], $data['quantity'])) {

            //     $intersectionForold = array_intersect($data['quantity'], $old_data_for_all['old_quantity_for_all']);

            //     $quantitymissingValuesforAll = array_diff($old_data_for_all['old_quantity_for_all'], $intersectionForold);
            // }
                // dd($updated_data);
            if ( isset($updated_data) && !empty($updated_data)) {
                $data  = $updated_data;
                $name = $this->get_by_id($this->_modal, $id)->ConstructionSite->name . ' ' . $this->get_by_id($this->_modal, $id)->ConstructionSite->surename;
                $to1 = 'pasquale.greengen@gmail.com';
                $to2 = 'angelica.greengen@gmail.com';
                $subject = 'Cambio configurazione Materiali';
                $this->materialChangeMail($to1, $subject, $name, $data, $construct_id);
                $this->materialChangeMail($to2, $subject, $name, $data, $construct_id);
                // $this->materialChangeMail($to1, $subject, $title, 'Materiali da cambiare', 'Materiali da rimuovere', 'Quantita da cambiare', 'Quantita da rimuovere');
                // $this->materialChangeMail($to2, $subject, $title, 'Materiali da cambiare', 'Materiali da rimuovere', 'Quantita da cambiare', 'Quantita da rimuovere');
                // $this->materialMail($to1, $subject, $title, '%materialname% %materialquantity% è stato cambiato in %newmaterialname% %newmaterialquantity%');
                // $this->materialMail($to2, $subject, $title, '%materialname% %materialquantity% è stato cambiato in %newmaterialname% %newmaterialquantity%');
            }

            // code for history
            if($historyData){
                $this->MaterialHistoryStore($historyData);
            }
           

            $msg = 'Material aggiornato.';
            $msg_s = 'success';
        } else {
            $msg_s = 'error';
            $msg = 'You have no materials';
        }

        return redirect()->route('construction_detail', ['id' => $construct_id, 'pagename' => 'Materiali'])->with($msg_s, $msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \{{ namespacedModel }} $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {  
       
        $updated_data = [];

        $id = $this->_request->material_delete_id;
      

        $Materialdata = $this->get_by_id($this->_modal, $id);
       
        $oldMaterialOptionName =  $Materialdata->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
        $old_name =  $Materialdata->MaterialList->name;

        $construct_id =  $Materialdata->construction_site_id != null ? $Materialdata->construction_site_id : $this->session_get('construction_id');
       
        $updated_data[] = [
            'material_option_name' => $oldMaterialOptionName,
            'old_material_name' => $old_name,
            'old_quantity' => $Materialdata->quantity
        ];

        $delation =  true;
        $data  = $updated_data;
        $name = $Materialdata->ConstructionSite->name . ' ' . $Materialdata->ConstructionSite->surename;
        $to1 = 'pasquale.greengen@gmail.com';
        $to2 = 'angelica.greengen@gmail.com';
        $subject = 'Cambio configurazione Materiali';

        if($Materialdata->delete_status == 0){
         
            $this->materialChangeMail($to1, $subject, $name, $data, $construct_id, $delation);
            $this->materialChangeMail($to2, $subject, $name, $data, $construct_id, $delation);
        
            if(in_array($Materialdata->MaterialList->MaterialTypeBelongs->name, ['Pannelli Fotovoltaici', 'Batterie', 'Inverter'])){
                $to = 'fotovoltaico.greengen@gmail.com';
                $this->materialChangeMail($to, $subject, $name, $data, $construct_id, $delation);
            }
        }
      
    
          $reason = $this->_request->reason;

          $this->MaterialDelete($this->_modal, $id);
        
          
          $materialHistoryData = [
            'construction_site_id' => $Materialdata->construction_site_id,
            'material_id' => $Materialdata->id,
            'changeBy' => Auth()->id(),
            'updated_field' => 'nome',
            'Original' => null,
            'Updated_to' => $Materialdata->MaterialList->name,
            'reason' => $reason,
        ];
            
        MatarialHistory::create($materialHistoryData);
    
       

        return redirect()->route('construction_detail', ['id' => $construct_id, 'pagename' => 'Materiali'])->with('success', 'Materiale eliminato con successo.');
    }


    public function deletePopupBodyGet($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        $reason = $data->delete_status == 0 ? 'Materiale eliminato' : 'Materiale ripristinato';

        // $reason  = $data->
        $updatedData = [
            'updated_field' => 'nome',
            'Original' => null,
            'Updated_to' => $data->MaterialList->name,
            'reason' => $reason
        ];

        $data =  view('response.MDeletePopupBody', ['updatedData' => $updatedData])->render();

        return response()->json(['htmlContent' => $data]);
    }

    /**
     * edit materials state
     */
    public function edit_materials_state($consId = null)
    {

        $construction_site_id  = $consId != null ? $consId : $this->session_get('construction_id');

        $data = $this->_request->except('_token');
       
        $update = [
            $data['toggleModalState'] => $data['toggleModalStatus'],
            'updated_by' => auth()->id()
        ];

        $this->get_by_id($this->_modal, $data['material_id'])->update($update);
        
        $Original = $data['toggleModalStatus'] == '1' ? 'no' : 'si';
        $updatedName = $data['toggleModalStatus'] == '1' ? 'si': 'no';

        $updatedData = [
            'construction_site_id' => $consId,
            'material_id' => $data['material_id'],
            'changeBy' => Auth()->id(),
            'updated_field' => $data['toggleModalState'],
            'Original' => $Original,
            'Updated_to' => $updatedName,
            'reason' => $data['reason']
        ];

        MatarialHistory::create($updatedData);

        return redirect()->route('construction_detail', ['id' => $construction_site_id, 'pagename' => 'Materiali'])->with('success', 'Material ' . $data['toggleModalState'] . ' aggiornato.');
    }

    // public function fetch_data_for_create_contract($id) {
    //     $option = [];
    //     $data = $this->get_by_column($this->_modal, 'construction_site_id', $id);
    
    //     foreach ($data as $key => $value) {
    //         if (!empty($value->matoption) && !empty($value->matoption->name) && !empty($value->matoption->MaterialType)) {
    //             $name = $value->matoption->name;
    //             $materialType = $value->MaterialList->MaterialTypeBelongs;
                
    //             // Check if the heading exists in the $option array
    //             if (!isset($option[$name])) {
    //                 $option[$name] = []; // Initialize the heading array if it doesn't exist
    //             }
    
    //             // Add the MaterialType to the heading array
    //             $option[$name][] = $materialType;
    //         }
    //     }
    //     // dd($option);
    //     return response()->json(['option' => $option]);
    // }

    public function fetch_data_for_create_contract($id) {
        $option = [];
        $data = $this->get_by_column($this->_modal, 'construction_site_id', $id);
       
        foreach ($data->where('material_list_id', '!=', null) as $key => $value) {
          
            if (!empty($value->matoption) && !empty($value->matoption->name) && !empty($value->matoption->MaterialType)) {
                $name = $value->matoption->name;
                $materialType = $value->MaterialList->MaterialTypeBelongs;
    
                // Check if the heading exists in the $option array
                if (!isset($option[$name])) {
                    $option[$name] = []; // Initialize the heading array if it doesn't exist
                }
    
                // Add the MaterialType to the heading array only if it's not already present
                if (!in_array($materialType, $option[$name])) {
                    $option[$name][] = $materialType;
                }
            }
        }
    
        // Optionally, you can sort the subarrays for consistency
        foreach ($option as &$subArray) {
            sort($subArray);
        }
        // dd($option);    
        return response()->json(['option' => $option]);
    }

    

    public  function get_mat_list_related_data(){
      $price =  null;
      $businessId = null;
       $construction_site_id = $this->_request->construction_site_id;
       $material_list_id = $this->_request->material_list_id;
       $Matlist = MaterialList::findorfail($material_list_id); 
       if($Matlist){
      
            $price = optional($Matlist->materialPriceRel)->where('business_detail_id', $this->_request->businessId)->pluck('materials_price_per_unit')->first();
            $businessId = optional($Matlist->materialPriceRel)->where('business_detail_id', $this->_request->businessId)->pluck('business_detail_id')->first();
        }
      $data =  $this->_modal->where('construction_site_id', $construction_site_id)->where('material_list_id', $material_list_id)->first();
      if($data){
        return response()->json([
            'quantity' => $data->quantity,
            'note' => $data->note,
            'price' => $price,
            'businessId' => $businessId
        ]);
      }
      return response()->json(['error' => 'Data not found'], 404);
     
    }

    public function business_check(){
        $data = $this->_request->id;
        $materialIds = $this->_request->MaterialsId;

     
        
        // Assuming $data contains the ID you want to find
        $businessDetail = BusinessDetail::find($data);

      
    
        // Use optional to prevent errors if $businessDetail is null
        $materialPrice = optional($businessDetail)->materialPrice()
                          ->whereIn('material_lists_id', $materialIds)
                          ->pluck('materials_price_per_unit')->toArray();
        $materialid = optional($businessDetail)->materialPrice()
        ->whereIn('material_lists_id', $materialIds)
        ->pluck('material_lists_id')->toArray();
        return response()->json(['materialPrice' => $materialPrice , 'materialListId' => $materialid] );
    }
}
