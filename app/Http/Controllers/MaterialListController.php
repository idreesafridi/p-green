<?php

namespace App\Http\Controllers;

use App\Models\MaterialList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConstructionMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class MaterialListController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_directory = 'materials';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, MaterialList $modal)
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
        $data['all'] = $this->get_all($this->_modal)
        ->sortBy(function ($item) {
            $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
            if ($name === 'cappotto') {
                return 1;
            } elseif ($name === 'termico') {
                return 2;
            } elseif ($name === 'fotovoltaico') {
                return 3;
            } else {
                return 4;
            }
        });

        return view($this->_directory . '.all', compact('data'));
        //$data['all'] = $this->get_all($this->_modal);
        //return view($this->_directory . '.all', compact('data'));
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
        $this->add($this->_modal, $data);

        return redirect()->route('construction_detail', ['id' => $this->session_get('construction_id'), 'pagename' => 'Materiali']);
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
     * @param  MaterialList  $modal
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
     * @param  MaterialList  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route($this->_directory . '_index');
    }

    /**
     * get material list by type id ajax
     */
    public function get_mat_list_ajax()
    {
        $data = $this->_request->except('_token', '_method');

        $list = $this->get_by_column($this->_modal, 'material_type_id', $data['id']);

        $result = view('response.get_mat_list_ajax', compact('list'))->render();

        return response()->json($result);
    }


    public function get_mat_list_ajax_for_report()
    {
        $data = $this->_request->except('_token', '_method');

        $construction_site_id=  $data['construction_site_id'];

        $ConstructionMaterial  = ConstructionMaterial::where('construction_site_id', $construction_site_id)->pluck('material_list_id')->toArray();

        // $list = $this->_modal->where('construction_site_id', $construction_site_id);

        $list = $this->get_by_column($this->_modal, 'material_type_id', $data['id'])->whereIn('id', $ConstructionMaterial);
     
        $result = view('response.get_mat_list_ajax_for_report', compact('list'))->render();

        return response()->json($result);
    }


    public function material_changing($construct_id){
        // Check if the user is logged in
        if (Auth::check()) {
            // User is logged in, redirect to the specified route
            return redirect()->route('construction_detail', [
                'id' => $construct_id,
                'pagename' => 'Materiali',
            ]);
        } else {
            // User is not logged in, attempt to log in
            $credentials = [
                // You need to provide the credentials for the user you want to log in
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
            ];
    
            // Attempt to log in the user
            if (Auth::attempt($credentials)) {
               
                // User is logged in, redirect to the specified route
                return redirect()->route('construction_detail', [
                    'id' => $construct_id,
                    'pagename' => 'Materiali',
                ]);
            } else {
                // Log in unsuccessful, redirect to the login page
                return Redirect::to('login')->with('redirect_url', route('construction_detail', [
                    'id' => $construct_id,
                    'pagename' => 'Materiali',
                ]));
            }
        }
    }


}
