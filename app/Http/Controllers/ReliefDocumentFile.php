<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RelDocFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReliefDocumentFile extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, RelDocFile $modal)
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
        
        try {
            $this->validate($this->_request, [
                'files.*' => 'mimes:pdf',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->with('error', 'Si prega di caricare solo file PDF');
        }
        if ($this->_request->file('files')) {

            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $fileName   =  $file->getClientOriginalName();
                $orignal_name = Str::before($file->getClientOriginalName(), '.');
                // dd($orignal_name);

                $path =  'Releif document files/' . $this->_request->relief_doc_f_name;
                // dd($path);
                // sent parameter to common download function
                $return_path = $this->upload_common_func($fileName, $path, $file);
                $data = [
                    'relief_doc_id' => $this->_request->relief_doc_id,
                    'construction_site_id' => $this->session_get("construction_id"),
                    'ref_folder_name' => $this->_request->relief_doc_f_name,
                    'file_name' => $orignal_name,
                    'file_path' => $return_path,
                    'state' => 1,
                    'updated_on' => date('Y-m-d'),
                    'updated_by' => Auth()->user()->name,
                ];
                $var = $this->add($this->_modal, $data);
                // dd($var);
            };
            $data2 = [
                // 'state' => 'Uploaded',
                'updated_on' => date('Y-m-d'),
                'updated_by' => Auth()->user()->name
            ];
            $prnt_id = ["id" =>  $this->_request->file_id];
            $var->ReliefDocument->update($data2);


            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);
            // UpdateReliefDocument();
            return redirect()->back()->with('success', 'file caricati con successo!');
            // return redirect()->route('show_relief_doc_file',$this->_request->relief_doc_id);
        }
    }
    // replace relif document file
    public function replace_file()
    {   
        // dd($this->_modal->find($this->_request->file_id));
        // dd($this->_request->bydefault);
        try {
            $this->validate($this->_request, [
                'file' => 'mimes:pdf',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->with('error', 'Si prega di caricare solo file PDF');
        }
        if ($this->_request->file('file')) {
            $file = $this->_request->file('file');
            if ($this->_request->bydefault != 0) {
                $fileName   =  $this->_request->orignal_name . '.pdf';
                $orignal_name = $this->_request->orignal_name;
            } else {
                $fileName = $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $fileName);
            }

            $path =  'Releif document files/' . $this->_request->relief_doc_f_name;
            // sent data to common function
            $return_path = $this->upload_common_func($fileName, $path, $file);
            $data = [
                'relief_doc_id' => $this->_request->relief_doc_id,
                'ref_folder_name' => $this->_request->relief_doc_f_name,
                'file_name' => $orignal_name,
                'file_path' => $return_path,
                'state' => 1,
                'updated_on' => date('Y-m-d'),
                'updated_by' => Auth()->user()->name,
            ];
            $data2 = [
                'state' => 1,
                'updated_on' => date('Y-m-d'),
                'updated_by' => Auth()->user()->name
            ];

            

          
            $prnt_id = ["id" =>  $this->_request->file_id];
            $var = $this->_modal->updateOrCreate($prnt_id, $data);
            if($var){
                if($var->ReliefDocument->folder_name ==  "Documenti Fotovoltaico"  && $var->ReliefDocument->state == 1 ){
                  
                    $var->ReliefDocument->update($data2);

                }
                else
                {
                    $var->ReliefDocument->update(['updated_on' => date('Y-m-d'), 'updated_by' => Auth()->user()->name]);
                }
           
    
            }
           
            
            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);

            return redirect()->back()->with('success', 'Il file è stato caricato con successo');
        }
    }
    // change_file
    public function change_file()
    {
        // dd("here");
    }

    // view sub file
    public function relif_sub_file($prnt_id)
    {
      
        $relief_doc_file = $this->get_by_id($this->_modal, $prnt_id);
        $construct_id  = $relief_doc_file->construction_site_id;
     
        $sub_chiled_f_name = $relief_doc_file->folder_name;
        return view('construction.construction_status.relief_doc_sub_file', compact('relief_doc_file', 'sub_chiled_f_name','construct_id'));
    }

    /**
     *  download file
     */
    public function download_file($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        $path = $data->file_path;
        return $this->download_common_func_for_files($path);
    }
    /**
     *  download file
     */
    public function download_folder($foldname)
    {
        $path = session()->get($foldname);
        return $this->download_zip_folder($foldname, $path);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  RelDocFile  $modal
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  RelDocFile  $modal
     * @return \Illuminate\Http\Response
     */
    /**change file status */

    // rec_file_destroy
    public function file_destroy()
    {
        $data = $this->get_by_id($this->_modal, $this->_request->id);
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');

        // $this->moveFileToTrash($data);

        // $getdata = [
        //     'file_path' =>  '',
        //     'updated_on' => '',
        //     'updated_by' => '',
        //     'file_name' => $data->file_name,
        // ];
        //  $data->update($getdata);

        //  $data2 = [
        //     'updated_on' => '',
        //     'updated_by' => ''
        // ];

        // $data->ReliefDocument->update($data2);
        // $prnt_id = ["id" => $this->_request->id];
        // $var = $this->_modal->updateOrCreate($prnt_id, $data);

        return redirect()->back()->with('error', 'Il file è stato eliminato.');
    }

    public function permanent_delete()
    {
        // dd('herewe'); 
        $data = $this->get_by_id($this->_modal, $this->_request->id);
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
        // $this->moveFileToTrash($data);
        
        // $getdata = [
        //     'file_path' =>  '',
        //     'updated_on' => '',
        //     'updated_by' => '',
        //     'file_name' => '',
        //     'state' => 0,
        // ];

        // $data->update($getdata);

        // $data2 = [
        //     'updated_on' => '',
        //     'updated_by' => ''
        // ];

        // $data->ReliefDocument->update($data2);

        return redirect()->back()->with('error', 'Il file è stato eliminato.');
       
    }


    public function delete_relief_folder($id){
        $model = $this->get_by_id($this->_modal, $id);
        $this->DeleteRecursiveReliefsub1Folders($model);

        return redirect()->back()->with('error', 'Dati eliminati correttamente');
    }
}
