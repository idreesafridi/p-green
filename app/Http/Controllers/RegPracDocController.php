<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegPracDoc;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Auth;
class RegPracDocController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, RegPracDoc $modal)
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
        // dd($this->_request->all());
        try {
            $this->validate($this->_request, [
                'fileTest' => 'mimes:pdf',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->with('error', 'Si prega di caricare solo file PDF');
        }

        //dd($this->_request->all());

        if ($this->_request->file('fileTest'))
        {
           
            // $file = $this->_request->file('file');
            $file = $this->_request->file('fileTest');
            $orignal_name = $this->_request->orignal_name;


            if($this->_request->orignal_name == 'Cila Protocollata 50-65-90'
                ||$this->_request->orignal_name == 'Cilas Protocollata 110'
                ||$this->_request->orignal_name == 'Delega Notifica Preliminare'
                ||$this->_request->orignal_name == 'Notifica Preliminare'
                ||$this->_request->orignal_name == 'Planimetria Catastale'
                ||$this->_request->orignal_name == 'Protocollo cila 50-65-90'
                ||$this->_request->orignal_name == 'Protocollo Cilas 110'
                )
            {
                $fileName   =  $this->_request->orignal_name.'.pdf';
                $orignal_name = $this->_request->orignal_name;
            }else
            {
                $fileName  =  $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $fileName);
            }
        }
        else if ($this->_request->file('file')) {
            $file = $this->_request->file('file');
            $orignal_name = $this->_request->orignal_name;

            if($this->_request->orignal_name == 'Notifica Preliminare')
            {

                $fileName   =  $this->_request->orignal_name.'.pdf';
                $orignal_name = $this->_request->orignal_name;
            }
        }

        // if($this->_request->file('Cila_protocollata_50_65_90'))
        // {

        //     $file = $this->_request->file('Cila_protocollata_50_65_90');
        //     // $fileName   =  'Cila protocollata 50-65-90.pdf';
        //     $fileName   =  'Cila protocollata 50-65-90';
        //     //
        // }
        // else if($this->_request->file('Cilas_protocollata_110'))
        // {
        //     $file = $this->_request->file('Cilas_protocollata_110');
        //     // $fileName   =  'Cilas protocollata 110.pdf';
        //     $fileName   =  'Cilas protocollata 110';
        //     //
        // }
        // else if($this->_request->file('Delega_Notifica_Preliminare'))
        // {
        //     $file = $this->_request->file('Delega_Notifica_Preliminare');
        //     // $fileName   =  'Delega Notifica Preliminare.pdf';
        //     $fileName   =  'Delega Notifica Preliminare';
        //     //
        // }
        // else if($this->_request->file('Notifica_Preliminare'))
        // {
        //     $file = $this->_request->file('Notifica_Preliminare');
        //     // $fileName   =  'Notifica Preliminare.pdf';
        //     $fileName   =  'Notifica Preliminare';
        //     //
        // }
        // else if($this->_request->file('Planimetria_Catastale'))
        // {
        //     $file = $this->_request->file('Planimetria_Catastale');
        //     // $fileName   =  'Planimetria Catastale.pdf';
        //     $fileName   =  'Planimetria Catastale';
        //     //
        // }
        // else if($this->_request->file('Protocollo_cila_50_65_90e'))
        // {
        //     $file = $this->_request->file('Protocollo_cila_50_65_90e');
        //     // $fileName   =  'Protocollo cila 50-65-90.pdf';
        //     $fileName   =  'Protocollo cila 50-65-90';
        //     //
        // }
        // else if($this->_request->file('Protocollo_cilas_110'))
        // {
        //     $file = $this->_request->file('Protocollo_cilas_110');
        //     // $fileName   =  'Protocollo cilas 110.pdf';
        //     $fileName   =  'Protocollo cilas 110';
        //     //
        // }
        //dd($fileName,$this->_request->status_regprac_id, $file, $this->_request->regprac_id);
        $this->store_file($fileName,$orignal_name,$this->_request->status_regprac_id, $file, $this->_request->regprac_id);
        return redirect()->back()->with('success','il file è stato caricato con successo!');
    }
    // file data store in data base common function
    public function store_file($fileName,$orignal_name, $id,$file,$regprac_id)
    {
        $construction_id = $this->session_get('construction_site_id');
        //dd($orignal_name);

        $path =  'Reg Practice File/';
        $return_path = $this->upload_common_func($fileName, $path, $file);
        $data = [
            'status_reg_prac_id'=>$id,
            'construction_site_id' => $this->session_get("construction_id"),
            'file_name'=> $orignal_name,
            'file_path'=> $return_path,
            'updated_on'=>date('Y-m-d'),
            'updated_by'=>Auth()->user()->name,
        ];

        $check_id = ["id"=>$regprac_id];
        $var = $this->_modal->updateOrCreate($check_id, $data);
        return $var;
    }
    /**multi files upload */
    public function reg_multi_files()
    {
        if ($this->_request->file('files'))
        {
            foreach ($this->_request->file('files') as  $files)
            {
                    $file = $files;
                    $fileName   =  $file->getClientOriginalName();
                    $orignal_name = str_replace('.pdf', '', $fileName);
                    // call to store file function
                    $this->store_file($fileName,$orignal_name,$this->_request->status_regprac_id, $file, $this->_request->regprac_id);
            };

            return back();
        }
    }
    /**
     * download file
     */
    public function download_regprac_files($id)
    {

            //   dd("download_reliefdoc", $id);
            $data = $this->get_by_id($this->_modal, $id);
            $path = $data->file_path;
            //---------------------------//
            return $this->download_common_func_for_files($path);
    }
    // download all files
    public function download_regprac_all_files()
    {
        // dd("here");
        // dd("download_reliefdoc", $foldname);
        // $data = $this->get_by_id($this->_modal, $id);

        $folder_name = "Reg Practice File";
        $path = "Reg Practice File";

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
     * @param  RegPracDoc  $modal
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
    //    return redirect()->back()->with('error','File has been deleted.');
       // return redirect()->route('{{ routeName }}');
   }
}
