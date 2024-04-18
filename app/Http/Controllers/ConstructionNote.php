<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstructionNotes;
use Illuminate\Http\Request;

class ConstructionNote extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, ConstructionNotes $modal)
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
    //remove priority
    public function remove_priority()
    {
        // check priority
        $check = $this->get_all($this->_modal);
        if (count($check) != 0) {
            foreach ($check as $check_item) {
                $priority =
                    [
                        'priority' => 0
                    ];
                $check_item->update($priority);
            }
        }
        return true;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        // check priority
        $this->remove_priority();

        $data = $this->_request->except('_token');
        $data['admin_id'] = auth()->id();
        $data['priority'] = 1;
        $this->add($this->_modal, $data);
        return redirect()->route('construction_detail', ['id' => $data['construction_site_id'], 'pagename' => 'Note']);
    }
    /**when click on start change his status*/
    public function click_on_start($id)
    {
        $check_priority = $this->get_by_id($this->_modal, $id);
        // check priority
        $priority = [];
        if ($check_priority->priority == 1) {
            // check priority
            $this->remove_priority();
            $priority['priority'] = 0;
        } else {
            // check priority
            $this->remove_priority();
            $priority['priority'] = 1;
        }
        $check_priority->update($priority);
        return back();
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
     * @param  ConstructionNote  $modal
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
     * @param  ConstructionNote  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->delete($this->_modal, $id);
        return back();
    }

    /**
     * search notes
     */
    public function search()
    {
        $note = $this->_request->searchNotes;
        
        $id = $this->session_get('construction_id');
        $notes = $this->_modal
            ->where('construction_site_id', $id)
            ->where(function ($query) use ($note) {
                $query->where('notes', 'LIKE', '%' . $note . '%')
                        ->orWhereHas('user', function ($subQuery) use ($note) {
                            $subQuery->where('name', 'LIKE', '%' . $note . '%');
                        });
            })
            ->get();

        //$notes = $this->_modal->where('notes', 'LIKE', '%' . $note . '%')->get();

        $result = view('response.notes-response', compact('notes'))->render();

        return response()->json($result);
    }
}
