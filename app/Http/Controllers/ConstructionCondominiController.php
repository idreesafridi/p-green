<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionCondomini;
use Illuminate\Http\Request;

class ConstructionCondominiController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionCondomini $modal)
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
        return view($this->_directory . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $condoArr = [
            'construction_site_id' => $this->_request->fk_id,
            'construction_assigned_id' => $this->_request->con_condo_id
        ];

       
        $this->get_by_column($this->_modal, 'construction_assigned_id', $this->_request->con_condo_id)->each(function ($item) {
            $item->delete();
        });

        // $this->get_by_column($this->_modal, 'construction_site_id', $this->_request->con_condo_id)->each(function ($item2) {
        //     $item2->delete();
        // });




        $var = $this->add($this->_modal, $condoArr);


        return redirect()->back()->with('success', 'Condomini assigned');
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
        return view($this->_directory . 'show', compact('data'));
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
        return view($this->_directory . 'edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \{{ namespacedModel }}  $modal
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

        $this->validate($this->_request, [
            'name' => 'required',
        ]);

        $data = $this->_request->except('_token', '_method');

        $data = $this->get_by_id($this->_modal, $id)->update($data);

        return redirect()->route($this->_directory . '_index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \{{ namespacedModel }} $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $id = $this->_request->condo_id;

        $data = $this->get_by_id($this->_modal, $id);
        $constructionSiteId = $data->construction_site_id;

        if ($this->_modal->where('construction_site_id', $constructionSiteId)->count() > 1) {
            $this->destroyById($this->_modal, $id);
        } else {
            $data->update(['construction_assigned_id' => null]);
        }
        return redirect()->back()->with('success', 'Condominio Deleted Successfully.');
    }

    /**
     * get condomini with ajax request
     *
     * @param  \{{ namespacedModel }} $modal
     * @return \Illuminate\Http\Response
     */
    public function getCondomini()
    {

        $data = $this->get_by_column($this->_modal, 'construction_site_id', $this->_request->id);

        $result = view('response.get_all_condomini', compact('data'))->render();

        return response()->json($result);
    }
}
