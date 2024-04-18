<?php

namespace App\Http\Controllers;

use App\Models\TypeOfDedectionFiles;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeOfDedectionFilesController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, TypeOfDedectionFiles $modal)
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

        // dd($this->_request->all());
        $sub2_id = $this->_request->type_of_dedection_sub2_id;
        $file_id = null;
        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                $this->store_file($filename, $file,$orignal_name,$file_id, $sub2_id,$this->_request->pr_not_doc_id,$this->_request->parent1_folder_name,$this->_request->parent2_folder_name,$this->_request->parent3_folder_name);
            }
            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);
        }
        return redirect()->back()->with('success','Il file è stato caricato con successo');
    }
     // file data store in data base common function
    private function store_file($filename, $file,$orignal_name,$file_id,$sub2_id,$pr_not_doc_id,$parent1_folder_name,$parent2_folder_name,$parent3_folder_name)
    {
        $path = $parent1_folder_name.'/'.$parent2_folder_name.'/'.$parent3_folder_name;

        //   dd($path);
        $return_path = $this->upload_common_func($filename, $path, $file);
          $data = [
            'type_of_dedection_sub2_id'=> $sub2_id,
            'construction_site_id' => $this->session_get("construction_id"),
            'file_name'=>$orignal_name,
            'file_path'=>$return_path,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
        ];
        $data2 = [
            // 'state' => 'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];
        $prnt_id = ["id" =>  $this->_request->file_id];
        $var = $this->_modal->updateOrCreate($prnt_id, $data);
         $consId  = $var->construction_site_id;
        $var->TypeOfDedectionSub2->update($data2);

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
          
          if($file_path =  $data->file_path  )
          {
              return $this->download_common_func_for_files($file_path);
          }
          else
          {
          
             $folder4Name = $data->folder_name;
          
            $folder3data =  $data->TypeOfDedectionSub2;
            
            $folder3Name = $folder3data->folder_name;
            
            $folder2data =  $folder3data->TypeOfDedectionSub1;
            
            $folder2Name = $folder2data->folder_name;
            
            $folder1Name = $folder2data->PrNotDoc->folder_name;
        
           return $this->DownloadSubFile($folder1Name, $folder2Name,$folder3Name, $folder4Name);
          }

    }

    // public function download_file($id)
    // {
    //       //   dd("download_reliefdoc", $id);
    //       $data = $this->get_by_id($this->_modal, $id);

    //         $file_path =  $data->file_path;       
           
    //         return $this->download_common_func_for_files($file_path);
    

    // }


    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show_prnt_files($id,$docname,$prntfname,$f1name,$f2name)
    {
        // dd($id,$docname,$prntfname,$f1name,$f2name);
        $prenoti_doc_file = $this->get_by_id($this->_modal, $id);
        // dd($prenoti_doc_file->TypeOfDedectionFiles2);
        $construct_id =   $prenoti_doc_file->construction_site_id;
        $parent_folder_name = $prenoti_doc_file->folder_name;
        return view('sub_doc_file2',compact('prenoti_doc_file','id','docname','prntfname','f1name','f2name', 'parent_folder_name', 'construct_id'));

    }
    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd("here");
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
     * @param  TypeOfDedectionFiles  $modal
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
        dd($this->_request->all());
    }

    // rec_file_destroy
    public function file_destroy()
    {
      
        $data = $this->get_by_id($this->_modal, $this->_request->id);

        if($data->file_name != null && $data->folder_name == null){
          $data->bydefault == 1 ? $this->softDelete($data) : $this->permanentDelete($data);
        }elseif($data->folder_name != null && $data->file_name == null  ){
            $data->TypeOfDedectionFiles2->each(function ($item) {
                $item->bydefault == 1 && $item->folder_name == null ? $this->softDelete($item) : $this->permanentDelete($item);
            });
        }

        
        // $this->moveFileToTrash($data);
        
        // $data = [
        //     'file_path'=>  null,
        //     'updated_on'=> null,
        //     'updated_by'=> null,
        //     'file_name'=>null,
        //     'state'=>0,
        // ]; 
        

        // $datamain = [
        //     'updated_on'=> null,  
        //     'updated_by'=> null,
        // ]; 


        // $var = $this->get_by_id($this->_modal, $this->_request->id);

        // if($var->file_name != null){
        //     $prnt_id = ["id" => $this->_request->id];
        //     $var = $this->_modal->updateOrCreate($prnt_id,$data);
    
    
        //     $var->TypeOfDedectionSub2()->update($datamain);
        // }
        // else{
        //     $var->update($datamain);
        //     $var->TypeOfDedectionFiles2()->update($data);

          
        // }
        
        return redirect()->back()->with('error','Il file è stato eliminato.');

    }
}
