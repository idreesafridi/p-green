<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, MaterialType $modal)
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
        // $all = $this->get_all($this->_modal);

        // return view({{ routeName }}, compact('all'));
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
     * @param  {{ model }}  $modal
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
     * @param  {{ model }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }
}
