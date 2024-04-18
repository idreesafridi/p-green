<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrNotDoc;
use App\Models\RelDocFile;
use File;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class PriNotiDocController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, PrNotDoc $modal)
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

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $prenoti_doc_file = $this->get_by_id($this->_modal, $id);
       
        if (
            $prenoti_doc_file->folder_name == "Documenti 50"
            || $prenoti_doc_file->folder_name == 'Documenti 65'
            || $prenoti_doc_file->folder_name == 'Documenti 90'
            || $prenoti_doc_file->folder_name == 'Documenti 110'
            || $prenoti_doc_file->folder_name == 'Documenti Fotovoltaico'
            || $prenoti_doc_file->folder_name == 'Documenti Sicurezza'
            || $prenoti_doc_file->folder_name == 'Documenti Assistenza'
        ) {
           
            $folder_name = $prenoti_doc_file->folder_name;
            $relief_doc_file = RelDocFile::where('construction_site_id', $prenoti_doc_file->construction_site_id)->where('file_name', 'Notifica Preliminare')->first();
          
            return view('sub1', compact('prenoti_doc_file', 'folder_name', 'relief_doc_file'));
        }
     
        return view('construction.construction_status.prenoti_doc_file', compact('prenoti_doc_file'));
    }

    //for navebar button 110,90,65,60
    public function check_50_65_90_110($slug, $consId =  null)
    {

        $constrct_id = $consId != null ? $consId : $this->session_get("construction_id");

        $prenoti_doc_file = $this->_modal->where('construction_site_id',$constrct_id)->where('folder_name',$slug)->first();

        $folder_name = $prenoti_doc_file->folder_name;
       
        return view('sub1', compact('prenoti_doc_file', 'folder_name'));
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
     * @param  PrNotDoc  $modal
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
     * down load all main folder inside files in zip formate
     */
    public function download_prenotidoc($id, $consId = null)
    {
       
        $data = $this->get_by_id($this->_modal, $id);
        if (
            $data->folder_name == "Documenti 50"
            || $data->folder_name == 'Documenti 65'
            || $data->folder_name == 'Documenti 90'
            || $data->folder_name == 'Documenti 110'
            || $data->folder_name == 'Documenti Fotovoltaico'
            || $data->folder_name == 'Documenti Sicurezza'
        ) {
            $folder_name = $data->folder_name;
        } else {
            $folder_name = "record";
        }

        $path = $id;

        $constrct_id = $consId != null ? $consId : $this->session_get("construction_id");

        $add = 'construction-assets/' . $constrct_id . '/' . $path;

        $folderPath = public_path($add);

        // Initialize an instance of ZipArchive
        $zip = new ZipArchive;
        // Create a new zip file and open it for writing
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
    // download folder
    public function download_prenotidoc_folder($foldername, $consId = null)
    {
  
        $folder_name = $foldername;
        
        if ($folder_name == "all") {
            $path = $foldername;
           
        }
        elseif ( $folder_name == "Documenti 50"
        || $folder_name == 'Documenti 65'
        || $folder_name == 'Documenti 90'
        || $folder_name == 'Documenti 110'
     
        || $folder_name == 'Documenti Sicurezza'
             ) {
            $path =   $folder_name;
             }
        
        else {
            // $path = '/PreNotification Document files/' . $folder_name;
            $path = "PreNotification Document files" . '/' . $folder_name;
            // dd($path);
        }

        // ---------------------------//
        return $this->download_zip_folder($folder_name, $path, $consId);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  PrNotDoc  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $data = $this->get_by_id($this->_modal, $id);
        $data->update(['state' => 0]);  
        return redirect()->back()->with('error', 'Dati eliminati correttamente');
    }



    public function DeleteAllPrinotdoc($id)
    {
        $model = $this->get_by_id($this->_modal, $id);
    //   dd($model);
    if(
        $model->folder_name == "Documenti Sicurezza"
    || $model->folder_name == 'Documenti 110'
    || $model->folder_name == 'Documenti 50'
    || $model->folder_name == 'Documenti 65'
    || $model->folder_name == 'Documenti 90'
    ){
        $this->updateStateRecursive1($model);
    }
    
    else{
        $this->updateStateRecursive($model);
    }

        return redirect()->back()->with('error', 'Dati eliminati correttamente');

    }
    
}
