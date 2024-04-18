<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RelifDocFileSub1;

class RelifDocFileSub1Controller extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, RelifDocFileSub1  $modal)
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
        // dd($this->_request->all());
        try {
            $this->validate($this->_request, [
                'files.*' => 'mimes:pdf',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->with('error', 'Si prega di caricare solo il file PDF');
        }
        
        $file_id = null;
        if ($this->_request->file('files'))
        {

            $path = $this->session_get($this->_request->rel_doc_file_folder_name);
            // dd($path);

            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                //
                $return_path = $this->upload_common_func($filename, $path, $file);
                $data = [
                    'rel_doc_file_id' => $this->_request->rel_doc_file_id,
                    'construction_site_id' => $this->session_get("construction_id"),
                    'rel_doc_file_folder_name' => $this->_request->rel_doc_file_folder_name,
                    'file_name' => $orignal_name,
                    'file_path' => $return_path,
                    'state'=>1,
                    'updated_on' => date('Y-m-d'),
                    'updated_by' => Auth()->user()->name,
                ];

                $var = $this->add($this->_modal, $data);
                $data2 = [
                    // 'state' => 'Uploaded',
                    'updated_on' => date('Y-m-d'),
                    'updated_by' => Auth()->user()->name
                ];

                $var->RelDocFile->update($data2);
                // $prnt_id = ["id" => $file_id];
                // $var = $this->_modal->updateOrCreate($prnt_id, $data);

               // sent data to common function
                $this->upload_common_func($filename, $path, $file);
            }
            // UpdateReliefDocument();
            return redirect()->back()->with('success','file caricati con successo!');
        }

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

        return redirect()->route($this->_directory . '_index');
    }

     /**
     *  download file
     */
    public function download_file($id)
    {
        //   dd("download_reliefdoc", $id);
          $data = $this->get_by_id($this->_modal, $id);
          $path = $data->file_path;
        //   dd($path);
          //---------------------------//
          return $this->download_common_func_for_files($path);
        // $data = $this->get_by_id($this->_modal, $id);
        // $path = 'Releif document files/' . $data->ref_folder_name . '/' . $data->relief_doc_id . '/' . $data->file_name;
        // return $this->download_common_func($data->path);
    }

    /**
     * sent email
     */
    public function rec_email()
    {
        dd("sent email");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \{{ namespacedRelifDocFileSub1 }} $modal
     * @return \Illuminate\Http\Response
     */
    public function permanent_delete()
    {

        $data = $this->get_by_id($this->_modal, $this->_request->id);
    
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
       // dd($this->_request->all());
            // $this->delete($this->_modal, $id);
        //     $data = [
        //      'file_path'=>  '',
        //      'updated_on'=> '',
        //      'updated_by'=> '',
        //      'file_name'=>'',
        //      'state'=>0,
        //  ];

        //  $prnt_id = ["id" => $this->_request->id];
        //  $var = $this->_modal->updateOrCreate($prnt_id,$data);

        //  $data2 = [
        //     'updated_on' => '',
        //     'updated_by' => '',
        // ];

        // $var->RelDocFile->update($data2);
         return redirect()->back()->with('error','Il file è stato eliminato.');
    }
}
