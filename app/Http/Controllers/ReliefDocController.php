<?php

namespace App\Http\Controllers;

use App\Models\ReliefDoc;
use App\Models\RelDocFile;
use Illuminate\Http\Request;
use App\Models\TypeOfDedectionSub1;
use App\Http\Controllers\Controller;

class ReliefDocController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ReliefDoc $modal)
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
        $this->session_store('reldocid', $id);
        $relief_doc_file = $this->get_by_id($this->_modal, $id);
        $construct_id =   $relief_doc_file->construction_site_id;

        return view('construction.construction_status.relief_doc_file', compact('relief_doc_file', 'construct_id', 'id'));
    }
    // check fotovoltac
    public function check_fotovoltac($slug, $consId =  null)
    {
        $constrct_id =  $consId != null ? $consId : $this->session_get("construction_id");

        $relief_doc_file = $this->_modal->where('construction_site_id', $constrct_id)->where('folder_name', $slug)->first();

        return redirect()->route('show_relief_doc_file', $relief_doc_file->id);
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
     * @param  ReliefDoc  $modal
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
     * download all files which is located in main folder
     */
    public function download_reliefdoc($id)
    {
        $data = $this->get_by_id($this->_modal, $id);
        $folder_name = $data->folder_name;
        $path = '/Releif document files/' . $folder_name;

        return $this->download_zip_folder($folder_name, $path);
    }
    /**
     * download_reliefdoc_folder
     */
    public function download_reliefdoc_folder($foldname)
    {
        $folder_name = $foldname;

        if ($folder_name == "all") {
            $path = '/Releif document files/';
        } else {
            $path = '/Releif document files/' . $foldname;
        }

        return $this->download_zip_folder($folder_name, $path);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ReliefDoc  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd("delete  reliefdoc");
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }

    public function DeleteAllReliefdoc($id)
    {

        $model = $this->get_by_id($this->_modal, $id);
       
        if (
            $model->folder_name == "Documenti Rilievo" 
        ) {
            $this->DeleteRecursiveReliefFolders($model);
        } else {
            // dd($model);
            $this->DeleteReliefFiles($model);
        }

        return redirect()->back()->with('error', 'Dati eliminati correttamente');
    }
}