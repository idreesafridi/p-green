<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\statusRegPrac;

class StatusRegPracController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, statusRegPrac $modal)
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
     * change the status status_regprac state
     */
    public function status_regprac($id)
    {
        $data = $this->_request->only('state', 'reminders_emails', 'reminders_days');
        $this->change_status($this->_modal, $id, $data, 'status_regprac');

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
    /**
     * Store a newly created resource in storage.
     *
     *file upload here
     */

    /**
     * Display the specified resource.
     *
     * @param  $this->_modal  $modal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $regprac = $this->get_by_id($this->_modal, $id);
        $regprac_folder_name = "Pratica Protocollata";
        $status_regprac_id = $id;
        $construct_id  = $regprac->construction_site_id; 
        return view('construction.construction_status.regprac', compact('regprac', 'regprac_folder_name', 'status_regprac_id','construct_id'));
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
     * @param  statusRegPrac  $modal
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
     * @param  statusRegPrac  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }
}
