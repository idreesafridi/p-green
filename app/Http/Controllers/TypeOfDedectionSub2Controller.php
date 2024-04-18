<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Models\TypeOfDedectionSub1;
use App\Models\TypeOfDedectionSub2;
use App\Http\Controllers\Controller;

class TypeOfDedectionSub2Controller extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, TypeOfDedectionSub2 $modal)
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
     * replace_file
     */
    public function replace_file()
    {

        // dd($this->_request->all());
        if ($this->_request->file('file')) {
            
            $file = $this->_request->file('file');
            if( $file->getClientOriginalExtension()  === 'pdf'){   
            if ($this->_request->bydefault == 1) {
                $filename   =  $this->_request->orignal_name . '.pdf';
                $orignal_name = $this->_request->orignal_name;
            } else {
                $filename = $file->getClientOriginalName();
                $orignal_name = str_replace('.pdf', '', $filename);
            }
            //
            $this->store_file($filename, $file, $orignal_name, $this->_request->file_id, $this->_request->pr_not_doc_id, $this->_request->type_of_dedection_sub1_id, $this->_request->parent1_folder_name, $this->_request->parent2_folder_name);
            //
            return redirect()->back()->with('success', 'Il file è stato caricato con successo');
        }
        else{
            return redirect()->back()->with('error', 'Si prega di caricare solo file PDF.');
        } 
        }
    }
    // /**multi files upload */
    public function upload_file()
    {
        // dd($this->_request->all());
        $file_id = null;
        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                $file = $files;
                $fileExtension = $file->getClientOriginalExtension();
            if ($fileExtension === 'pdf') {
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                $this->store_file($filename, $file, $orignal_name, $file_id, $this->_request->pr_not_doc_id, $this->_request->type_of_dedection_sub1_id, $this->_request->parent1_folder_name, $this->_request->parent2_folder_name);
            }else{
                return redirect()->back()->with('error', 'Si prega di caricare solo file PDF.');
            }
            }


            $publicPath = public_path();

            // Set the desired permissions (777 in this case)
            $permissions = 0777;

            // Recursively change permissions for files and directories within the public folder
            $this->recursiveChmod($publicPath, $permissions);
        }
        return redirect()->back()->with('success', 'Il file è stato caricato con successo');
    }
    // file data store in data base common function
    public function store_file($filename, $file, $orignal_name, $file_id = null, $pr_not_doc_id, $type_of_dedection_sub1_id, $parent1_folder_name, $parent2_folder_name)
    {
        $consId = TypeOfDedectionSub1::find($type_of_dedection_sub1_id)->construction_site_id;
       
        $path = $parent1_folder_name . '/' . $parent2_folder_name;
        $return_path = $this->upload_common_func($filename, $path, $file, $consId);
        $data = [
            'type_of_dedection_sub1_id' => $type_of_dedection_sub1_id,
            'construction_site_id' => $consId,
            'file_name' => $orignal_name,
            'file_path' => $return_path,
            'state' => 1,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
        ];
        $data2 = [
            // 'state'=>'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];
        $prnt_id = ["id" => $file_id];
        $var = $this->_modal->updateOrCreate($prnt_id, $data);
        $var->TypeOfDedectionSub1()->update($data2);

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
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show_prnt_files($prnotdocid, $sub1id, $id, $docname, $prntfname)
    {   
       
        $this->session_store('prnotdocid', $prnotdocid);
        $this->session_store('sub1id', $sub1id);
        $this->session_store('id', $id);
        $this->session_store('docname', $docname);
        $this->session_store('prntfname', $prntfname);

        $prenoti_doc_sub2 = $this->get_by_id($this->_modal, $id);
         $construct_id =   $prenoti_doc_sub2->construction_site_id;
       
        $sub_chiled_f_name = $prenoti_doc_sub2->folder_name;

        return view('sub_document_files', compact('prenoti_doc_sub2', 'prnotdocid', 'sub1id', 'docname', 'prntfname', 'sub_chiled_f_name','construct_id'));
    }

    /**
     * down load all main folder inside files in zip formate
     */
    // this download function for file
    public function download_sub2($id)
    {

        //    dd("download_reliefdoc", $id);
        $data = $this->get_by_id($this->_modal, $id);
        $path = $data->file_path;
        //  dd($path);
        //---------------------------//
        return $this->download_common_func_for_files($path);
    }
    // download sub folder2
    public function download_subfolder2($id)
    {
        $data = $this->get_by_id($this->_modal, $id);

        $folder_name = $data->folder_name;

        $path = $id;
        if($data){
           $constrct_id =    $data->construction_site_id; 
        }else{
            $constrct_id = $this->session_get("construction_id");
        }

       
        $add = 'construction-assets/' . $constrct_id . '/' . $path;

        $folderPath = public_path($add);

        $zip = new ZipArchive;

        $zipFileName = $folder_name . '.zip';

        $zip->open(public_path($zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // Iterate through all the files in the folder
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing it
        $zip->close();
        // Download the zip file
        return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
    }

    // email sent to owner
    public function email()
    {
        dd($this->_request->all());
    }
    public function download_sub2_folder($fol1, $fol2, $fol3)
    {
        // dd($fol1,$fol2,$fol3);
        $folder_name = $fol3;
        // dd($folder_name);
        if ($folder_name == "all") {
            $path = $fol1 . '/' . $fol2;
        } else {
            $path = $fol1 . '/' . $fol2 . '/' . $fol3;
        }

        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path);
    }
    

    // rec_file_destroy
    public function file_destroy()
    {
        $data = $this->get_by_id($this->_modal, $this->_request->id);
      
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');
        
        // $this->moveFileToTrash($data);
        // // $data = $this->get_by_id($this->_modal, $this->_request->id);
        // $data = [
        //     'file_path' =>  '',
        //     'updated_on' => '',
        //     'updated_by' => '',
        //     'file_name' => $data->file_name,
        // ];

        // $prnt_id = ["id" => $this->_request->id];
        // $var = $this->_modal->updateOrCreate($prnt_id, $data);

        // $data2 = [
        //     'updated_on' => '',
        //     'updated_by' => '',
        // ];
        
        // $var->TypeOfDedectionSub1()->update($data2);
        return redirect()->back()->with('error', 'Il file è stato eliminato.');
    }
    public function permanent_delete()
    {
        $data = $this->get_by_id($this->_modal, $this->_request->id);
       
        $data->file_name != null && $data->bydefault == 0 ?  $this->permanentDelete($data) : ($data->file_name != null && $data->bydefault == 1 ? $this->softDelete($data) : '');

        // $data = [
        //     'file_path' =>  '',
        //     'updated_on' => '',
        //     'updated_by' => '',
        //     'file_name' => '',
        //     'state' => 0,
        // ];

        // $prnt_id = ["id" => $this->_request->id];
        // $var = $this->_modal->updateOrCreate($prnt_id, $data);

        // $data2 = [
        //     'updated_on' => '',
        //     'updated_by' => '',
        //   ];

        // $var->TypeOfDedectionSub1()->update($data2);


        return redirect()->back()->with('error', 'Il file è stato eliminato.');
    }

    public function delete_sub2_folder($id) 
    {
    
        $model = $this->get_by_id($this->_modal, $id);
        // dd($model);  
            $this->Deletesub2Recursive($model);
    
            return redirect()->back()->with('error', 'Dati eliminati correttamente');

    }  
}
