<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrNotDocFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class PriNotiDocFileController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, PrNotDocFile $modal)
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
        // dd($this->session_get("construction_id"));

        try {
            $this->validate($this->_request, [
                'files.*' => 'mimes:pdf',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
          
            return redirect()->back()->withInput()->with('error', 'Si prega di caricare solo file PDF');
        }   
            
        $construction_id = $this->session_get('construction_site_id');
        if ($this->_request->file('files'))
        {
            $constrct_id = $this->session_get("construction_id");
            foreach ($this->_request->file('files') as  $files)
            {
                    $file = $files;
                 
                    $fileName   =  $file->getClientOriginalName();
                    $orignal_name = Str::before($file->getClientOriginalName(), '.');
                    $path =  'PreNotification Document files/'.$this->_request->prenoti_doc_f_name;
                    $return_path = $this->upload_common_func($fileName, $path, $file);

                    $data = [
                        'pr_not_doc_id'=>$this->_request->prenoti_doc_id,
                        'construction_site_id' => $this->session_get("construction_id"),
                        'folder_name'=> $this->_request->prenoti_doc_f_name,
                        'file_name'=> $orignal_name,
                        'file_path'=> $return_path,
                        'updated_on'=> date('Y-m-d'),
                        'updated_by'=> Auth()->user()->name,
                    ];

                    $var = $this->add($this->_modal, $data);
              
            };
            $data2 = [
                'updated_on'=>date('Y-m-d'),
                'updated_by'=>Auth()->user()->name
            ];
            $var->PrNotDoc->update($data2);


                   // Define the path to the public folder
            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);

            return redirect()->back()->with('success','Il file è stato caricato con successo');
        }
    }

    // replace file
    public function replace_file()
    {

    
         if ($this->_request->file('file'))
         {
             $file = $this->_request->file('file');

             if($this->_request->bydefault == 1)
            {
               
                $fileName   =  $this->_request->orignal_name.'.pdf';
                $orignal_name = $this->_request->orignal_name;
            }else{
            
                $fileName = $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $fileName);
            }
             $this->store_file($fileName, $file,$orignal_name,$this->_request->file_id,$this->_request->parent_folder_name,$this->_request->pr_not_doc_id);

             return redirect()->back()->with('success','Il file è stato caricato con successo');
         }
    }
     // file data store in data base common function
     public function store_file($fileName,$file,$orignal_name,$file_id,$parent_folder_name, $pr_not_doc_id)
     {
        $construction_id = $this->session_get('construction_site_id');

        $path =  'PreNotification Document files/'.$parent_folder_name.'/';

        $return_path = $this->upload_common_func($fileName, $path, $file);

         $data = [
            'pr_not_doc_id'=>$pr_not_doc_id,
            'construction_site_id' => $this->session_get("construction_id"),
            'folder_name'=> $parent_folder_name,
            'file_name'=> $orignal_name,
            'file_path'=> $return_path,
            'updated_on'=>date('Y-m-d'),
            'updated_by'=>Auth()->user()->name,
        ];
        $data2 = [
            'updated_on'=>date('Y-m-d'),
            'updated_by'=>Auth()->user()->name
        ];

         $prnt_id = ["id" => $file_id];
         $var = $this->_modal->updateOrCreate($prnt_id, $data);
         $var->PrNotDoc->update($data2);

         $publicPath = public_path();
               // Set the desired permissions (777 in this case)
          $permissions = 0777;

        // Recursively change permissions for files and directories within the public folder
        $this->recursiveChmod($publicPath, $permissions);

           return $var;
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
     *  download file
     */
    public function download_prenoti_file($id)
    {
        //  dd("download_reliefdoc", $id);
       $data = $this->get_by_id($this->_modal, $id);
       $path = $data->file_path;
    //    dd($path);
       //---------------------------//
       return $this->download_common_func_for_files($path);
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
     * @param  PrNotDocFile  $modal
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


  // rec_file_destroy
  public function file_destroy()
  {
    

     $data = $this->get_by_id($this->_modal, $this->_request->id);

     $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
    //  $this->moveFileToTrash($data);

    //   $updatedata = [
    //       'file_path'=>  '',
    //       'updated_on'=> '',
    //       'updated_by'=> '',
    //       'file_name'=>$data->file_name,
    //   ];
    //   $data->update($updatedata);

    //   $data2 = [
    //   'updated_on'=> '',
    //     'updated_by'=> '',
    // ];
    
    //  $data->PrNotDoc()->update($data2);

      return redirect()->back()->with('error','Il file è stato eliminato.');
  }
  public function permanent_delete()
  { 
        $data = $this->get_by_id($this->_modal, $this->_request->id);
    
        if($data){
            $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
        }

       return redirect()->back()->with('error','Il file è stato eliminato.');
  }
}
