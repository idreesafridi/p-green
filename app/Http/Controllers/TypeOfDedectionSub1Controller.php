<?php

namespace App\Http\Controllers;

use App\Models\TypeOfDedectionSub1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class TypeOfDedectionSub1Controller extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, TypeOfDedectionSub1 $modal)
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
        // return view({{ view_name }});
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
            'name' => 'required',
        ]);

        $data = $this->_request->except('_token');
        $var = $this->add($this->_modal, $data);

        return redirect()->route('{{routeName}}');
    }
    // replace file
    public function replace_file()
    {
        // dd('here');

        if ($this->_request->file('file')) {
            $file = $this->_request->file('file');
            if ($file->getClientOriginalExtension() === 'pdf') {
            if ($this->_request->bydefault == 1) {
                $filename   =  $this->_request->orignal_name . '.pdf';
                $orignal_name = $this->_request->orignal_name;
            } else {
                $filename = $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $filename);
            }
            $this->store_file($filename, $file, $orignal_name, $this->_request->file_id, $this->_request->pr_not_doc_id, $this->_request->parent1_folder_name);
        }else{
            return redirect()->back()->with('error', 'Seleziona solo file PDF.');
        }
            return redirect()->back()->with('success', 'Il file è stato caricato con successo');
        }
    }
    // /**multi files upload */
    public function upload_file()
    {
        if(isset($this->_request->construction_id)){
            $construction_id = $this->_request->construction_id;
        } 
        $file_id = null;
        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                
                $file = $files;
                if ($file->getClientOriginalExtension() === 'pdf') {
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                $this->store_file($filename, $file, $orignal_name, $file_id, $this->_request->pr_not_doc_id, $this->_request->parent1_folder_name, $construction_id);
                }else{
                    return redirect()->back()->with('error', 'eleziona solo file PDF.'); 
                }
            }
        }
        return redirect()->back()->with('success', 'Il file è stato caricato con successo');
    }
    // file data store in data base common function
    public function store_file($filename, $file, $orignal_name, $file_id = null, $pr_not_doc_id, $parent1_folder_name, $construction_id = null)
    {

        // dd($pr_not_doc_id);
        $construction_site_id = $construction_id != null ? $construction_id : $this->session_get("construction_id");
        $path = $parent1_folder_name;

        $return_path = $this->upload_common_func($filename, $path, $file);
        $data = [
            'pr_not_doc_id' => $pr_not_doc_id,
            'construction_site_id' => $construction_site_id,
            'file_name' => $orignal_name,
            'file_path' => $return_path,
            //  'state'=>1,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
        ];
        $prnt_id = ["id" => $file_id];
        $var = $this->_modal->updateOrCreate($prnt_id, $data);
        //
        $data2 = [
            // 'state'=>'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];
        $prnt1st = ["id" => $var->id];
        $var->PrNotDoc()->update($data2);
        return $var;
    }
    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function sub2document($pr_not_doc_id, $id, $docname)
    {
        $prenoti_doc_sub1 = $this->get_by_id($this->_modal, $id);
        $parent_folder_name = $prenoti_doc_sub1->folder_name;
        $construct_id = $prenoti_doc_sub1->construction_site_id;   
        return view('sub_document_folder', compact('prenoti_doc_sub1', 'pr_not_doc_id', 'docname', 'parent_folder_name','construct_id'));
    }
    // downlod all files
    public function download_sub1_folder($fol1, $fol2)
    {
        // dd($fol1,$fol2);
        $folder_name = $fol2;
        // dd($folder_name);
        if ($folder_name == "all") {
            $path = $fol1;
        } else {
            $path = $fol1 . '/' . $fol2;
        }
        // dd($path);
        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path);
    }
    /**
     * down load all main folder inside files in zip formate
     */
    /**
     * down load all main folder inside files in zip formate
     */
    public function download_sub1($id)
    {
        //   dd("download_reliefdoc", $id);
        $data = $this->get_by_id($this->_modal, $id);
        $path = $data->file_path;
        //---------------------------//
        return $this->download_common_func_for_files($path);
    }
    // email sent to owner
    public function email()
    {
        // dd($this->_request->all());
    }
public function file_destroy()
{
    
    $data = $this->get_by_id($this->_modal, $this->_request->id);
    
    $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');

    //  $this->moveFileToTrash($data);
    
    // // $data = $this->_modal->find($this->_request->id);
   
    // $data->update([
    //     'file_path' => '',
    //     'updated_on' => '',
    //     'updated_by' => '',
    //     'file_name' => $data->file_name,
    // ]);

    // $data->PrNotDoc->update([
    //     'updated_on' => '',
    //     'updated_by' => '',
    // ]);

    return redirect()->back()->with('error', 'Il file è stato eliminato.');
}

    public function permanent_delete()
    {
          $data = $this->get_by_id($this->_modal, $this->_request->id);
        

        $this->moveFileToTrash($data);

        // $data  = $this->_modal->find($this->_request->id);
        $dataupdate = [
            'file_path' =>  '',
            'updated_on' => '',
            'updated_by' => '',
            'file_name' => '',
            'state' => 0,
        ];
        $data->update($dataupdate);

        $data2 = [
            // 'state'=>'Uploaded',
            'updated_on' => '',
            'updated_by' => '',
        ];
        
        $data->PrNotDoc()->update($data2);


        return redirect()->back()->with('error', 'Il file è stato eliminato.');
    }


    public function DeleteAllsub1($id) 
    {
    
        $model = $this->get_by_id($this->_modal, $id);
        // dd($model);  
            $this->Deletesub1Recursive($model);
    
            return redirect()->back()->with('error', 'Dati eliminati correttamente');

    }  
}