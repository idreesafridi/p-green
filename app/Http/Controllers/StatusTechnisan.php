<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StatusTechnician;

class StatusTechnisan extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, StatusTechnician $modal)
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
     * change the status preanalysis state
     */
    public function status_technisan($id)
    {
        $data = $this->_request->only('state', 'tecnician_id', 'reminders_emails', 'reminders_days');

        if ($data['tecnician_id'] == null && $data['state'] == null) {
            $data['state'] = null;
        } elseif ($data['tecnician_id'] == null) {
            $data['state'] = 'Not Assigned';
        } else {
            $data['state'] = 'Assigned';
        }

        $this->change_status($this->_modal, $id, $data, 'status_technisan');

        // email sent to technician if technician is assign
        $exist = $this->get_by_id($this->_modal, $id);

        if ($exist != null) {
            if ($exist->user != null) {
                $email = $exist->user->email;
                $to = $email;
                $subject = 'Sei stato assegnato | Cantiere' . ' ' . $exist->name;
                $data =
                    [
                        'name' => $exist->name,
                        'email' => $email
                    ];
                $path = 'emails.ta';
                $this->email_against_missing_files($to, $subject, $data, $path);
            }
        }

        //
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
     * @param  StatusTechnician  $modal
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
     * @param  StatusTechnician  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->delete($this->_modal, $id);
        return redirect()->route('{{ routeName }}');
    }
}
