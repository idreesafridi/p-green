<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TechinicianDetailRequest;
use App\Models\TechincianDetail;
use App\Models\User;
use Illuminate\Http\Request;

class TechincianDetailController extends Controller
{
    private $_request = null;
    private $_modal = null;
    private $_role = 'technician';

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, TechincianDetail $modal)
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
    public function create($id)
    {
        $this->session_store('techno_id', $id);

        $user = $this->user_by_id_with_role(new User(), $id, $this->_role);

        if ($user != null && $user->techincian()->count() == 0) {
            return view($this->_role . '.add_techno_details', compact('user'));
        } else {
            $this->session_remove('techno_id');
            return redirect()->route('allUsers', $this->_role);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TechinicianDetailRequest $validation)
    {
        $id = $this->session_get('techno_id');

        $user = $this->user_by_id_with_role(new User(), $id, $this->_role);

        if ($user != null && $user->techincian()->count() == 0) {
            $data = $validation->validated();
            $data['user_id'] = $id;

            $this->add($this->_modal, $data);

            return redirect()->route('allUsers', $this->_role);
        } else {
            $this->session_remove('techno_id');
            return redirect()->route('allUsers', $this->_role);
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
     * @param  TechincianDetail  $modal
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
     * @param  TechincianDetail  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }
}
