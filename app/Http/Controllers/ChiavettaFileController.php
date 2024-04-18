<?php

namespace App\Http\Controllers;

use File;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\Http\Controllers\Controller;
use App\Models\ChiavettaFile;
use App\Models\ChiavettaDoc;
use Illuminate\Http\Request;

class ChiavettaFileController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ChiavettaFile $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id, $folder_id)
    {
        $construct_id =  isset($id) ? $id : $this->session_get("construction_id");

        $folder = ChiavettaDoc::where('id', $folder_id)->first();
        $data['Files'] = ChiavettaFile::where('construction_site_id', $construct_id)->where('chiavetta_doc_id', $folder_id)->get();

        return view('doc_chiavetta_files', compact('construct_id', 'data', 'folder')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if(isset($this->_request->construction_id)){
            $construction_id = $this->_request->construction_id;
        } 
        //dd($construction_id);
        $file_id = null;
        if ($this->_request->file('files')) {
            foreach ($this->_request->file('files') as  $files) {
                
                $file = $files;
                if ($file->getClientOriginalExtension() === 'pdf') {
                $filename  =  $file->getClientOriginalName();
                $path_parts = pathinfo($file->getClientOriginalName());
                $orignal_name = $path_parts['filename'];
                $this->store_file($filename, $file, $orignal_name, $file_id, $this->_request->parent_folder_id, $this->_request->parent_folder_name, $construction_id);
                }else{
                    return redirect()->back()->with('error', 'eleziona solo file PDF.'); 
                }
            }
        }
        return redirect()->back()->with('success', 'Il file è stato caricato con successo');
    }

    public function store_file($filename, $file, $orignal_name, $file_id, $parent_folder_id, $parent_folder_name, $construction_id)
    {

        //$construction_site_id = $construction_id != null ? $construction_id : $this->session_get("construction_id");
        $path = $parent_folder_name;

        $return_path = $this->upload_common_func($filename, $path, $file);
        $data = [
            'chiavetta_doc_id' => $parent_folder_id,
            'construction_site_id' => $construction_id,
            'file_name' => $orignal_name,
            'file_path' => $return_path,
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name,
        ];
        $prnt_id = ["id" => $file_id];
        $var = $this->_modal->updateOrCreate($prnt_id, $data);
        //$var = $this->add($this->_modal,$data);
        $data2 = [
            // 'state'=>'Uploaded',
            'updated_on' => date('Y-m-d'),
            'updated_by' => Auth()->user()->name
        ];
        $var->ChiavettaDoc()->update($data2);
        return $var;
    }

    public function replace_file()
    {
        //dd('here');

        if(isset($this->_request->construction_id)){
            $construction_id = $this->_request->construction_id;
        } 
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
            $this->store_file($filename, $file, $orignal_name, $this->_request->file_id, $this->_request->parent_folder_id, $this->_request->parent_folder_name, $construction_id);
        }else{
            return redirect()->back()->with('error', 'Seleziona solo file PDF.');
        }
            return redirect()->back()->with('success', 'Il file è stato caricato con successo');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //dd("here");
        $id = $this->_request->id;
        $this->destroyById($this->_modal, $id);

        return redirect()->back()->with('success', 'File eliminato con successo.');
    }

    public function DeleteAllChiavettaFiles($folder_id, $consId)
    {
        //dd('here');
        ChiavettaFile::where('chiavetta_doc_id', $folder_id)->where('construction_site_id', $consId)->delete();

        return redirect()->back()->with('success', 'File eliminato con successo.');
    }

    public function zip_chiavetta_files($folder_id, $consId)
    {
        //dd("download here");

        $folder = ChiavettaDoc::where('id', $folder_id)->first();
        $folder_name = $folder->folder_name;

        $add = 'construction-assets/' . $consId . '/' . $folder->folder_name;

        $folderPath = public_path($add);
        // dd($folderPath );
        // Initialize an instance of ZipArchive
        $zip = new ZipArchive;
        // Create a new zip file and open it for writing
        $zipFileName = $folder_name . '.zip';
        // Check if the folder path exists
        if (File::exists($folderPath)) {
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
                    $substring = 'Releif document files';
                    if (preg_match("/$substring/", $filePath)) {
                        $relativePath = substr($filePath, strlen($folderPath));
                    } else {
                        $relativePath = substr($filePath, strlen($folderPath) + 1);
                    }
                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing it
            $zip->close();
            // Download the zip file
            return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        } else {

            return redirect()->back()->with('error', "La directory è vuota!!");
        }
    }
}
