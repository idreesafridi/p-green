<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionMaterial;
use App\Models\ConstructionShippingList;
use Illuminate\Http\Request;

class ConstructionSiteShippingListController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = '';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionShippingList $modal)
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
        $data = $this->_request->except('_token');
        $newArr = [];

        $construction_shipping_id = $data['construction_shipping_id'];

        $uncheckShipList = $this->_modal->where('construction_shipping_id', $construction_shipping_id)
            ->whereNotIn('construction_material_id', $data['centri_material_id'])->get();

        foreach ($uncheckShipList as $uncheckedList) {
            $uncheckedList->ship_change = 0;
            $uncheckedList->update();
        }

        if (array_key_exists('construction_shipping_id', $data)) {
            if (array_key_exists('centri_material_id', $data)) {
                for ($i = 0; $i < count($data['centri_material_id']); $i++) {
                    $construction_material_id = $data['centri_material_id'][$i];
                    $shipchange = $data['shipchange'][$i];

                    
                    $checkShipList = $this->_modal->where('construction_shipping_id', $construction_shipping_id)
                        ->where('construction_material_id', $construction_material_id)->first();

                    if ($checkShipList != null) {
                        if ($shipchange == 1) {
                            $consMaterial = ConstructionMaterial::select('quantity')->where('id', $construction_material_id)->first();
                           
                            
                            $totalQty = $consMaterial->quantity;

                            // getting old date_add
                            $old_qty = $checkShipList['qty'];
                            $new_formed_qty = $data['qty'][$i] + $old_qty;
                            $new_rem_qty = $totalQty - $new_formed_qty;

                            $newArr['construction_shipping_id'] = $construction_shipping_id;
                            $newArr['construction_material_id'] = $construction_material_id;
                            $newArr['total_qty'] = $totalQty; //construction material table
                            $newArr['shipping_truck'] = $data['shipping_truck'];
                            $newArr['qty'] = $new_formed_qty;;
                            $newArr['rem_qty'] = $new_rem_qty;

                            $checkShipList->update($newArr);
                        }
                    } else {
                        $consMaterial = ConstructionMaterial::select('quantity')->where('id', $construction_material_id)->first();

                        $totalQty = $consMaterial->quantity;

                        $newArr['construction_shipping_id'] = $construction_shipping_id;
                        $newArr['construction_material_id'] = $construction_material_id;
                        $newArr['total_qty'] = $totalQty;
                        $newArr['shipping_truck'] = $data['shipping_truck'];
                        $newArr['qty'] = $data['qty'][$i];
                        $newArr['ship_change'] = $shipchange;
                        $newArr['rem_qty'] = $totalQty - $data['qty'][$i];

                        $this->_modal->create($newArr);
                    }

                    $consMaterialUpdate = ConstructionMaterial::findOrfail($construction_material_id);
                    $consMaterialUpdate->update(['consegnato' => 1]);
                }
            }

            return response()->json('ok');
        } else {
            return response()->json('else');
        }
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
        $this->delete($this->_modal, $id);
        return redirect()->route($this->_directory . '_index');
    }
}
