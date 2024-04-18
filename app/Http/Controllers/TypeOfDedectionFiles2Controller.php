<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeOfDedectionFiles2;
use Illuminate\Support\Facades\Http;

class TypeOfDedectionFiles2Controller extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, TypeOfDedectionFiles2 $modal)
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
        $all = $this->get_all($this->_modal);

        return view('{{ routeName }}', compact('all'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $sub2_id = $this->_request->type_of_dedection_file_id;

        $file_id = null;
        $construction_site_id = $this->_request->construction_site_id;

        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                // dd($sub2_id,$filename,$file,$orignal_name ,$this->_request->parent1_folder_name,$this->_request->parent2_folder_name,$this->_request->parent3_folder_name,$this->_request->parent4_folder_name, $construction_site_id);
                $this->store_file($sub2_id, $filename, $file, $orignal_name, $this->_request->parent1_folder_name, $this->_request->parent2_folder_name, $this->_request->parent3_folder_name, $this->_request->parent4_folder_name, $construction_site_id);
            }

            $publicPath = public_path();

            $permissions = 0777;

            $this->recursiveChmod($publicPath, $permissions);
        }
        return back();
    }
    // file data store in data base common function
    private function store_file($sub2_id, $filename, $file, $orignal_name, $parent1_folder_name, $parent2_folder_name, $parent3_folder_name, $parent4_folder_name, $construction_site_id = null)
    {

        $path = $parent1_folder_name . '/' . $parent2_folder_name . '/' . $parent3_folder_name . '/' . $parent4_folder_name;
        if ($construction_site_id) {
            $consId = $construction_site_id;
        } else {
            $consId =  $this->session_get("construction_id");
        }
        $return_path = $this->upload_common_func($filename, $path, $file, $consId);
        $data = [
            'type_of_dedection_file_id' => $sub2_id,
            'construction_site_id' => $consId,
            'file_name' => $orignal_name,
            'file_path' => $return_path,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
            'state' => '1',
        ];
        $data2 = [
            // 'state' => 'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];

        $var = $this->_modal->updateOrCreate($data);

        $var->TypeOfDedectionFiles->update($data2);

        // sent data to common function
        $this->upload_common_func($filename, $path, $file, $consId);

        return $var;
    }

    /**
     * down load all main folder inside files in zip formate
     */
    public function download_sub2_chiled_file($id)
    {
        //   dd("download_reliefdoc", $id);
        $data = $this->get_by_id($this->_modal, $id);
        $path = $data->file_path;
        //---------------------------//
        return $this->download_common_func_for_files($path);
    }
    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show_prnt_files($id, $docname, $prntfname, $f1name, $f2name)
    {
        // dd($id,$docname,$prntfname,$f1name,$f2name);
        $prenoti_doc_file = $this->get_by_id($this->_modal, $id);
        return view('sub_doc_file2', compact('prenoti_doc_file', 'id', 'docname', 'prntfname', 'f1name', 'f2name'));
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
        return view('{{view_name}}', compact('data'));
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
        return view('{{view_name}}', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  TypeOfDedectionFiles2  $modal
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate($this->_request, [
            'name' => 'required',
        ]);

        $data = $this->_request->except('_token', '_method');

        $data = $this->get_by_id($this->_modal, $id)->update($data);

        return redirect()->route('{{routeName}}.index');
    }


    // email sent to owner
    public function email()
    {
        // dd($this->_request->all());
    }

    // rec_file_destroy
    public function file_destroy()
    {

        $data = $this->get_by_id($this->_modal, $this->_request->id);
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
        // $this->moveFileToTrash($data);
        // $data = [
        //     'file_path' =>  '',
        //     'updated_on' => '',
        //     'updated_by' => '',
        //     'file_name' => '',
        //     'state' => 0,
        // ];


        // $data2 = [
        //     // 'state' => 'Uploaded',
        //     'updated_on' => '',
        //     'updated_by' => '',
        // ];



        // $prnt_id = ["id" => $this->_request->id];
        // $var = $this->_modal->updateOrCreate($prnt_id, $data);

        // $var->TypeOfDedectionFiles->update($data2);

        return redirect()->back()->with('error', 'Il file è stato eliminato.');
    }
}
