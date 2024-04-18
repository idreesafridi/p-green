<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leg10File;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Auth;
use Illuminate\Support\Str;
class Legge10FileController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, Leg10File $modal)
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
     *file upload here
     */
    public function store()
    {
        if ($this->_request->file('file'))
        {
            $file = $this->_request->file('file');

            $orignal_name = $this->_request->orignal_name;

            if($orignal_name =="Ape Regionale" || $orignal_name == 'Legge 10' || $orignal_name == 'Ricevuta Ape Regione')
            {

                $fileName   =  $this->_request->orignal_name.'.pdf';
                $orignal_name = $this->_request->orignal_name;
            }else
            {
                $fileName  =  $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $fileName);
            }

            //
        }
        // if ($this->_request->file('Ape_regionale')) {
        //     $file = $this->_request->file('Ape_regionale');
        //     $fileName   =  'Ape regionale.pdf';
        //     $orignal_name = 'Ape regionale';
        //     //
        // } else if ($this->_request->file('Legge10')) {
        //     $file = $this->_request->file('Legge10');
        //     $fileName   =  'Legge 10.pdf';
        //     $orignal_name = 'Legge 10';
        //     //
        // } else if ($this->_request->file('Ricevuta_Ape_Regione')) {
        //     $file = $this->_request->file('Ricevuta_Ape_Regione');
        //     $fileName   =  'Ricevuta Ape Regione 10.pdf';
        //     $orignal_name = 'Ricevuta Ape Regione';
        //     //
        // }
        $path =  'Legge10 File/';
        // $path =  'Legge10 File/' . $this->_request->status_leg10_id . '/';

        // dd($fileName, $path, $file);
        $return_path = $this->upload_common_func($fileName, $path, $file);
        // dd($path.'/'.$return_path);
        // dd($orignal_name);
        $this->store_file($return_path,$orignal_name, $this->_request->status_leg10_id, $file, $this->_request->statuslegg_id);
        return redirect()->back()->with('success','il file è stato caricato con successo!');
    }
    /**multi files upload */
    public function legge10_multifile_upload()
    {
        $path =  'Legge10 File/';
        $statuslegg_id = '';
        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $fileName   =  $file->getClientOriginalName();
                $orignal_name = Str::before($file->getClientOriginalName(), '.');
                 // sent data to common function
                 // $this->upload_common_func($filename, $path, $file);

                $return_path = $this->upload_common_func($fileName, $path, $file);
                $this->store_file($return_path,$orignal_name, $this->_request->status_leg10_id, $file,$statuslegg_id);
            }
            return redirect()->back()->with('success','il file è stato caricato con successo!');
        }

    }
    // file data store in data base common function
    public function store_file($return_path,$orignal_name, $id, $file, $statuslegg_id = null)
    {
        $data = [
            'status_leg10_id' => $id,
            'construction_site_id' => $this->session_get("construction_id"),
            'file_name' => $orignal_name,
            'file_path'=>$return_path,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
        ];
        $data2 = [
            // 'state' => 'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];
        $prnt_id = ["id" => $statuslegg_id];
        $var = $this->_modal->updateOrCreate($prnt_id, $data);
        $var->StatusLegge10->update($data2);
        return $var;
    }
    /**
     * download file
     */
    public function download_legg10_file($id)
    {

        // $data = $this->get_by_id($this->_modal, $id);
        // $path =  'Legge10 File/' . $data->status_leg10_id . '/' . $data->file_name;
        // return $this->download_common_func($path);
         //   dd("download_reliefdoc", $id);
         $data = $this->get_by_id($this->_modal, $id);
         $path = $data->file_path;
         //---------------------------//
         return $this->download_common_func_for_files($path);
    }
    // legg 10 all files
    public function download_legg10_all_file()
    {
        $folder_name = "Legge10 File";
        // dd($folder_name);
            $path = "Legge10 File";
        // ---------------------------//
        return $this->download_zip_folder($folder_name,$path);
    }
    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $legge10file = $this->get_by_column($this->_modal, 'status_leg10_id', $id);
        $legge10_folder_name = "Legge 10";
        $status_leg10_id = $id;
        $construct_id  = $legge10file->first()->construction_site_id;
        
        return view('construction.construction_status.legge10file', compact('legge10file', 'legge10_folder_name', 'status_leg10_id', 'construct_id'));
    }
    /**
     * sent email
     */
    public function legg10_email()
    {
        dd("sent email");
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
     * @param  Leg10File  $modal
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
        // dd($this->_request->all());
          // $this->delete($this->_modal, $id);
        $data = $this->get_by_id($this->_modal, $this->_request->id);
        $this->moveFileToTrash($data);

       $data = [
           'file_path'=>  '',
           'updated_on'=> '',
           'updated_by'=> '',
           'file_name'=>$data->file_name,
       ];

       $prnt_id = ["id" => $this->_request->id];
       $var = $this->_modal->updateOrCreate($prnt_id,$data);

       return redirect()->back()->with('error','Il file è stato eliminato.');
       // return redirect()->route('{{ routeName }}');
   }
   public function permanent_delete()
   {
    //   dd($this->_request->all());
           // $this->delete($this->_modal, $id);
           $data = $this->get_by_id($this->_modal, $this->_request->id);
        $this->moveFileToTrash($data);
        
           $data = [
            'file_path'=>  '',
            'updated_on'=> '',
            'updated_by'=> '',
            'file_name'=>'',
            'state'=>0,
        ];

        $prnt_id = ["id" => $this->_request->id];
        $var = $this->_modal->updateOrCreate($prnt_id,$data);

        return redirect()->back()->with('error','Il file è stato eliminato.');
    //   permanent delet code
    //    $this->destroyById($this->_modal,$this->_request->id);
    //    return redirect()->back()->with('error','File has beed deleted.');
       // return redirect()->route('{{ routeName }}');
   }
}
