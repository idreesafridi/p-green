<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StatusPrNoti;
use App\Models\ReliefDoc;
use App\Models\ConstructionSite;
use Auth;
use PDO;

class StatusPreNotiController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, StatusPrNoti $modal)
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
     * change the status status_prenoti
     */
    public function status_prenoti($id)
    {
        $data = $this->_request->only('state', 'reminders_emails', 'reminders_days');
        $this->change_status($this->_modal, $id, $data, 'status_prenoti');

        return response()->json([
            'status' => 200,
            'message' => 'success',
        ]);
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
    
        // here i get id from session and pass to model and then get type of deduction data using relationship
        $construction = $this->get_by_id($this->_modal, $id);
 
        $construction_id = $construction->construction_site_id;
        //$construct_id = $this->session_get("construction_id");
        $this->session_store('backid', $id);

        $userRole = Auth::user(); 

        $var = $this->get_by_id(new ConstructionSite, $construction_id);

        if($userRole->hasRole('business') || $userRole->hasRole('worker')){
            
            return redirect()->route('construction_detail', ['id' => $construction_id, 'pagename' => 'Immagini', 'image' => 'cantiere']);
        
        }

        $prenoti_doc['prenoti_doc'] = $this->get_by_id($this->_modal, $id);
        $prenoti_doc['prenoti_and_relif'] = $this->get_by_column(new ReliefDoc, 'construction_site_id', $construction_id);

        return view('construction.construction_status.prenoti_doc', compact('prenoti_doc', 'var'));
    }

    // download prenoti folder
    public function download_prenoti_folder()
    {
        $folder_name = 'PreNotification Document files';
        $path = '/PreNotification Document files/';
        return $this->download_zip_folder($folder_name, $path);
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
     * @param  StatusPrNoti  $modal
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
     * @param  StatusPrNoti  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }

    public function filter_documents() 
    {
        //dd("here");
        //dd($this->_request->all());
        $construct_id = $this->_request->id;
        $var = $this->get_by_id(new ConstructionSite, $construct_id);

        $slug = $this->_request->slug;
        if($slug == 'Documenti 110') {
            $prdoc = 'Documenti 110';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Diagnosi Energetica', 'Schemi Impianti', 'Pratiche Comunali'])->get();
        }
        else if($slug == 'Documenti 90') {
            $prdoc = 'Documenti 90';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Diagnosi Energetica', 'Pratiche Comunali'])->get();
        }
        else if($slug == 'Documenti 65') {
            $prdoc = 'Documenti 65';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Diagnosi Energetica', 'Schemi Impianti', 'Pratiche Comunali'])->get();
        }
        else if($slug == 'Documenti 50') {
            $prdoc = 'Documenti 50';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Schemi Impianti'])->get();
        }
        else if($slug == 'Documenti Fotovoltaico') {
            $prdoc = 'Dico';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->whereIn('folder_name', ['Documenti Fotovoltaico', 'Schemi Impianti'])->get();
        }
        else {
            $prdoc = 'all';
            $prenoti_doc['prenoti_doc'] = StatusPrNoti::where('construction_site_id', $construct_id)->first();
            $prenoti_doc['prenoti_and_relif'] = ReliefDoc::where('construction_site_id', $construct_id)->get();
        }

        $result =  view('response.filtered_documents', compact('prenoti_doc', 'prdoc', 'var'))->render();
        return response()->json([
            'data' => $result,
        ]);
    }
}
