<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionShipping;
use Illuminate\Http\Request;

class ConstructionSiteShippingController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionShipping $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    private function centri_material_list_view($var)
    {
        return view('response.centri-material-list', compact('var'))->render();
    }

    private function getLatestShipping()
    {
        $var = $this->_modal->latest()->first();
        return $this->centri_material_list_view($var);
    }

    private function getAllShipping()
    {
        $cons = $this->get_all($this->_modal);
        $result = view('response.shipping-list-response', compact('cons'))->render();
        $resultBadge = view('response.centri-head-badg', compact('cons'))->render();

        return [
            'result' => $result,
            'resultBadge' => $resultBadge,
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping = $this->getAllShipping();

        if ($this->_request->id == null) {
            $centriMaterialList = $this->getLatestShipping();
        } else {
            $var = $this->get_by_id($this->_modal, $this->_request->id);
            $centriMaterialList = $this->centri_material_list_view($var);
        }

        return response()->json(['result' => $shipping['result'], 'centriHeaderBadg' => $shipping['resultBadge'], 'centriMaterialList' => $centriMaterialList]);
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
        $this->validate($this->_request, [
            'centryval' => 'required',
        ]);

        $data['construction_site_id'] = $this->_request->centryval;
        $var = $this->add($this->_modal, $data);

        $shipping = $this->getAllShipping();
        $centriMaterialList = $this->centri_material_list_view($var);

        return response()->json(['result' => $shipping['result'], 'centriHeaderBadg' => $shipping['resultBadge'], 'centriMaterialList' => $centriMaterialList]);
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
    public function destroy($id)
    {
        $this->destroyById($this->_modal, $id);

        $shipping = $this->getAllShipping();
        $centriMaterialList = $this->getLatestShipping();

        return response()->json(['result' => $shipping['result'], 'centriHeaderBadg' => $shipping['resultBadge'], 'centriMaterialList' => $centriMaterialList]);
    }

    /**
     * Print shipping construction sites
     */
    public function print_shipping()
    {
        $all = $this->get_all($this->_modal);
        return View('construction.print_shipping', compact('all'));
    }
}
