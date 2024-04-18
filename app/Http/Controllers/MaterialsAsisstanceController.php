<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PrNotDoc;
use App\Models\PrNotDocFile;
use Illuminate\Http\Request;
use App\Models\MaterialsAsisstance;
use App\Models\TypeOfDedectionSub1;
use App\Http\Controllers\Controller;
use App\Models\ConstructionMaterial;

class   MaterialsAsisstanceController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = 'materials';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, MaterialsAsisstance $modal)
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
        return view($this->_directory . '.all', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view($this->_directory . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate($this->_request, [
            'machine_model' => 'required',
        ]);
        if (isset($this->_request->construction_site_id)) {

            $construct_id = $this->_request->construction_site_id;
        } else {
            $construct_id = $this->session_get("construction_id");
        }

        $construction_material =  ConstructionMaterial::find($this->_request->machine_model);
        $construction_MaterialList_name = $construction_material->MaterialList->name;
        $data = $this->_request->only(

            'freshman',
            'start_date',
            'invoice',
            'report',
            'notes',
            'state',
        );


        $data['state'] = "Da completare";
        $data['construction_site_id'] = $construct_id;
        $data['construction_material_id'] = $this->_request->machine_model;
        $data['machine_model'] = $construction_MaterialList_name;

        // $data['expiry_date'] = Carbon::parse($this->_request->start_date)->addYears(1);
        $data['expiry_date'] = $this->_request->start_date;
        $data['updated_by'] = Auth()->user()->name;

        // dd($data);
        $this->add($this->_modal, $data);

        // $folder_name = $data['machine_model'] . '_' . $data['start_date'];
        // dd($data['start_date']);

        $year = \Carbon\Carbon::parse($data['expiry_date'])->format('Y');




        // $folder_name = $data['notes'] . '_' . $year;
        $folder_name = $construction_MaterialList_name . ' ' . $year;

        $this->createAssistanceFolder($folder_name, $construct_id);

        // return redirect()->route('construction_detail', ['id' => $construct_id, 'pagename' => 'Materials'])->with('success', 'assistanse record updated');
        return redirect()->back()->with('success', 'assistanse record saved');
    }

    /**
     * view all assistanse
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        if (request()->date) {
            $date = request()->date;
        } else {
            $date = Carbon::now();
        }
        $month = Carbon::parse($date)->month;
        $year = Carbon::parse($date)->year;
        $data = $this->_modal->whereMonth('expiry_date', $month)->whereYear('expiry_date', $year)->get();

        if (count($data) > 0) {
            $formattedDate = $date instanceof Carbon ? $date->format('F Y') : null;
        } else {
            $formattedDate = null;
        }
        // $data = $this->_modal->whereMonth('start_date', $month)->get();

        $to_be_completed = $this->_modal->whereMonth('expiry_date', $month)->whereYear('expiry_date', $year)->where('state', 'Da completare')->count();
        $Completato =  $this->_modal->whereMonth('expiry_date', $month)->whereYear('expiry_date', $year)->where('state', 'Completato')->count();

        return view('assistances', compact('data', 'to_be_completed', 'Completato', 'formattedDate'));
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
     * @param  MaterialsAsisstance  $modal
     * @return \Illuminate\Http\Response
     */
    // public function update_assistance()
    // {
    //     $data = $this->_request->except('_token');

    //     if (array_key_exists('assistanse_id', $data)) {
    //         for ($i = 0; $i < count($data['assistanse_id']); $i++) {
    //             $id = $data['assistanse_id'][$i];

    //             $loopArr = [
    //                 'assistanse_id' => $data['assistanse_id'][$i],
    //                 'machine_model' => $data['machine_model'][$i],
    //                 'freshman' => $data['freshman'][$i],
    //                 'start_date' => $data['start_date'][$i],
    //                 'expiry_date' => $data['expiry_date'][$i],
    //                 'state' => $data['state'][$i],
    //                 'notes' => $data['notes'][$i]
    //             ];
    //             // dd($loopArr);



    //             $update_mat = $this->get_by_id($this->_modal, $id);
    //             // dd($update_mat);
    //             $date1 = Carbon::parse($update_mat->expiry_date);
    //             $date2 = Carbon::parse($loopArr['expiry_date']);

    //             // if($date1 != $date2 || $data['state'][$i] == 'Completato'){
    //             if ($data['state'][$i] == 'Completato') {
    //                 unset($loopArr['assistanse_id']);
    //                 // Check if the record with the same data already exists
    //                 $existing_record = MaterialsAsisstance::where($loopArr)->first();

    //                 $loopArr['assistanse_id'] = $data['assistanse_id'][$i];
    //                 if (!$existing_record) {
    //                     $new_data = $update_mat->replicate();

    //                     $new_expiry_date = Carbon::parse($loopArr['expiry_date'])->addYear();

    //                     $new_data->expiry_date = $new_expiry_date;

    //                     $new_data->state = 'Da completare';

    //                     unset($loopArr['expiry_date']);
    //                     unset($loopArr['state']);


    //                     $new_data->fill($loopArr);

    //                     $new_data->id = null;

    //                     $new_data->save();

    //                     $loopArr['expiry_date'] = $data['expiry_date'][$i];
    //                     $loopArr['state'] = $data['state'][$i];
    //                 }
    //             } else if ($data['state'][$i] == 'Da completare' && $update_mat->state = 'Completato') {
    //                 // dd($update_mat->construction_material_id);
    //                 // dd($update_mat->id);
    //                 $data1 =  MaterialsAsisstance::where('construction_material_id', $update_mat->construction_material_id)->where('id', '!=',  $update_mat->id)->get();
    //                 //    dd($data1);
    //                 foreach ($data1 as $recoard) {
    //                     // dd($recoard);
    //                     $index = array_search($recoard->id, $data['assistanse_id']);
    //                     if ($index !== false) {
    //                         unset($data['assistanse_id'][$index]);
    //                         unset($data['machine_model'][$index]);
    //                         unset($data['freshman'][$index]);
    //                         unset($data['start_date'][$index]);
    //                         unset($data['expiry_date'][$index]);
    //                         unset($data['state'][$index]);
    //                         unset($data['notes'][$index]);
    //                     }
    //                     $recoard->delete();
    //                 }



    //                 $update_mat->update($loopArr);
    //             }
    //             if (isset($loopArr['expiry_date'])) {

    //                 $update_mat->update($loopArr);
    //             }
    //         }
    //     }
    //     return redirect()->back()->with('success', 'assistanse record updated');
    // }

    public function update_assistance()
    {
        $arrMaterial = [];
        $loopArr = [];

        $data = $this->_request->except('_token');

        if (isset($data['assistanse_id']) != null) {
            foreach ($data['assistanse_id'] as $key => $d) {
                $dataArr = [
                    $id =  $data['assistanse_id'][$key],
                    $machine_model =  $data['machine_model'][$key],
                    $freshman =  $data['freshman'][$key],
                    $start_date =  $data['start_date'][$key],
                    $expiry_date =  $data['expiry_date'][$key],
                    $notes =  $data['notes'][$key],
                    $state =  $data['state'][$key],
                ];

                array_push($loopArr, $dataArr);
                $getMaterials = MaterialsAsisstance::findOrFail($data['assistanse_id'][$key]);

                array_push($arrMaterial, $getMaterials);

                $dataArr = [];
            }
            foreach ($arrMaterial as $key => $material) {

                $checkArr = $loopArr[$key];
                $materialRecord = MaterialsAsisstance::find($material->id);

                if (!$materialRecord) {
                    // Record doesn't exist, you can handle this case if needed
                    continue; // Skip to the next iteration
                }



                $getSetMaterial = MaterialsAsisstance::findOrFail($material->id);
                if ($getSetMaterial) {

                    if ($material->state == $checkArr[6]) {
                        $getSetMaterial->expiry_date = $checkArr[4];
                        $getSetMaterial->notes = $checkArr[5];
                        $getSetMaterial->freshman = $checkArr[2];
                        $getSetMaterial->update();
                    } else {

                        if ($checkArr[6] == 'Completato') {
                            $new_data = $material->replicate();
                            $new_expiry_date = date('Y-m-d', strtotime($checkArr[4] . ' +1 year'));
                            $new_data->expiry_date = $new_expiry_date;
                            $new_data->notes = $checkArr[5];
                            $new_data->freshman = $checkArr[2];
                            $new_data->save();

                            $getSetMaterial->state = $checkArr[6];
                            $getSetMaterial->expiry_date = $checkArr[4];
                            $getSetMaterial->notes = $checkArr[5];
                            $getSetMaterial->freshman = $checkArr[2];
                            $getSetMaterial->update();

                            $construct_id = $getSetMaterial->construction_site_id;
                            $year = \Carbon\Carbon::parse($new_expiry_date)->format('Y');

                            $folder_name = $getSetMaterial->machine_model . ' ' . $year;

                            $this->createAssistanceFolder($folder_name, $construct_id);
                        } else {
                            $materialArr = MaterialsAsisstance::where('construction_site_id', $material->construction_site_id)
                                ->where(function ($query) use ($checkArr, $material) {
                                    $query->where('construction_material_id', $material->construction_material_id)
                                        ->where('machine_model', $checkArr[1])
                                        ->Where('start_date', $checkArr[3]);
                                })
                                ->where('id', '>', $checkArr[0]) // Add this condition to filter by 'id'
                                ->get(); // Delete the matching records
                            // ->delete(); // Delete the matching records

                            foreach ($materialArr as $arr) {

                                $targetId = $arr->id;
                                $index = array_search($targetId, array_column($arrMaterial, 'id'));
                                if ($index !== false) {
                                    $year = \Carbon\Carbon::parse($arr->expiry_date)->format('Y');

                                    $folder_name = $arr->machine_model . ' ' . $year;

                                    $parent = "Documenti Assistenza";

                                    $pr_not_doc = PrNotDoc::where('construction_site_id', $material->construction_site_id)->where('folder_name', $parent)->first();

                                    $pr_not_doc->TypeOfDedectionSub1()->where('folder_name', $folder_name)->delete();

                                    unset($arrMaterial[$index]);
                                }
                                $arrMaterial = array_values($arrMaterial);
                            }

                            $materialArr = MaterialsAsisstance::where('construction_site_id', $material->construction_site_id)
                                ->where(function ($query) use ($checkArr, $material) {
                                    $query->where('construction_material_id', $material->construction_material_id)
                                        ->where('machine_model', $checkArr[1])
                                        //                                ->Where('freshman', $checkArr[2])
                                        ->Where('start_date', $checkArr[3]);
                                    //                                ->Where('notes', $checkArr[5]);
                                })
                                ->where('id', '>', $checkArr[0]) // Add this condition to filter by 'id
                                ->delete(); // Delete the matching records

                            $getSetMaterial = MaterialsAsisstance::findOrFail($material->id);
                            if ($getSetMaterial) {
                                $getSetMaterial->state = $checkArr[6];
                                $getSetMaterial->expiry_date = $checkArr[4];
                                $getSetMaterial->notes = $checkArr[5];
                                $getSetMaterial->freshman = $checkArr[2];
                                $getSetMaterial->update();
                            }
                            // break;
                        }
                        //    array_push($finalArr, $checkArr);
                    }
                } else {
                    return redirect()->back()->with('success', 'Assistenza aggiornata');
                }
            }
       
        return redirect()->back()->with('success', 'Assistenza aggiornata');
     }else{
        
        return redirect()->back(); 
     }
    }
    /**change date */
    public function change_date()
    {

        $data = $this->get_by_id($this->_modal, $this->_request->assistanse_id);

        $data['start_date'] = $this->_request->change_date;
        $data['expiry_date'] = Carbon::parse($this->_request->change_date)->addYears(1);
        $data['updated_by'] = Auth()->user()->name;

        $data->update();
        return redirect()->back()->with('success', 'La data è stata modificata con successo!');
    }
    /**skip_this_year */
    public function skip_this_year()
    {

        $data = $this->get_by_id($this->_modal, $this->_request->assistanse_id);

        $data['expiry_date'] = Carbon::parse($data->start_date)->addYears(1);
        $data['updated_by'] = Auth()->user()->name;

        $data->update();
        return redirect()->back()->with('success', "Salta la data quest'anno con successo!");
    }
    /**mark completed */
    public function completed()
    {
        $data = $this->get_by_id($this->_modal, $this->_request->assistanse_id);

        $data['state'] = 'Completato';
        $data['updated_by'] = Auth()->user()->name;

        $data->update();
        return redirect()->back()->with('success', 'lo stato viene modificato con successo!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  MaterialsAsisstance  $modal
     * @return \Illuminate\Http\Response
     */
    public function delete_assistance()
    {
        $this->destroyById($this->_modal, $this->_request->assistanse_id);

        return redirect()->back()->with('success', 'Record eliminato con successo!');
    }
}
