<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BusinessDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AddUserRequest;

class UserController extends Controller
{
    private $_request = null;
    private $_modal = null;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct(Request $request, User $modal)
    {
        $this->_request = $request;
        $this->_modal = $modal;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role = null)
    {
        $data['roles'] = $this->all_roles();
        $data['user_role'] = $role;
        $data['users'] = [];
      
        if ($data['user_role'] != null) {
            if (in_array($data['user_role'], $data['roles'])) {
                $data['users'] = $this->user_by_role($this->_modal, $data['user_role']);
            }
        } else {
            $data['users'] = $this->get_all_users($this->_modal);
        }

        //dd($userCount = count($data['users']));

        return view('all_users', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userrole)
    {
        return view('create_user', compact('userrole'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddUserRequest $validation, $role)
    {
        if (in_array($role, $this->all_roles())) {
            $password = Hash::make($this->_request->name);
            $data = $this->_request->only('name', 'email', 'phone', 'residence_city');
            $data['password'] = $password;
            $data['orignalpass'] = $password;

            $user = $this->add($this->_modal, $data);
            $user->assignRole($role);

            if ($role == 'business') {
                $business_user = $validation->safe()->only('company_name', 'company_type');
                $user->business()->updateOrCreate($business_user);
            }

            // welcome email sent to construction owner
            $to = $this->_request->email;
            $subject = '!! Accedi al nuovo portale GREENGEN';
            $data = [
                'email' => $this->_request->email,
                'password' => $this->_request->name
            ];
            $path = 'emails.mail-benvenuto';
            $this->email_against_missing_files($to, $subject, $data, $path);

            $status = 'success';
            $msg = $role . ' created successfully';
        } else {
            $status = 'error';
            $msg = 'Invalid field record.';
        }

        return redirect()->route('allUsers', $role)->with($status, $msg);
    }

    /**
     * Display the specified resource.
     *
     * @param $this->_modal $modal
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
     * @param $this->_modal $modal
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
     * @param  \Illuminate\Http\Request $request
     * @param  User $modal
     * @return \Illuminate\Http\Response
     */
    public function update($id, $role = null)
    {
        $data = $this->_request->except('_token', '_method', 'update');
        $user = $this->get_by_id($this->_modal, $id);

        if ($user->roles[0]->name == $role) {
            if ($user['email'] != $data['email']) {
                $this->validate($this->_request, [
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            }
            $user->update($data);
            if ($role == 'business') {
                $business_data = $this->_request->only('company_name', 'company_type');
                $user->business()->update($business_data);
            }
            return redirect()->route('allUsers', $role)->with('success', 'Dati utente aggiornato');
        } else {
            return redirect()->route('allUsers')->with('error', 'Controlla i dettagli');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $modal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->destroyById($this->_modal, $id);
        return redirect()->back()->with('success', 'Utente eliminato');
    }

    public function passwordSendRequest()
    {
        $to = $this->_request->useremail;
        $from = 'greengen@password.com';

        $data = $this->get_by_column_single($this->_modal, 'email', $this->_request->useremail)->toArray();

        Mail::send('emails.mail-benvenuto-pass', ['data' => $data], function ($message) use ($from, $to) {
            $message->from($from);
            $message->to($to);
            $message->subject('!! Le tue credentiali di accesso');
        });

        return redirect()->back()->with('success', 'La password è stata inviata con successo a ' . $data['name']);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('q');
      
        $users = $this->_modal->where('email', 'like', '%'.$query.'%')->pluck('email')->toArray();
    
        return response()->json($users);
    }

    public function business_users(){

        // $business_users  = $this->user_by_role($this->_modal, 'business');
        // dd($business_users);

        // $business_users = BusinessDetail::all()->user();
        // dd($business_users);

        $business_users = BusinessDetail::with('user')->get();
       
        // $business_users = collect();

        // foreach ($businessDetails as $businessDetail) {
        //     $business_users->push($businessDetail->user);
        // }

        // dd($business_users);


        // Assuming $business_users is an array of user objects
    return response()->json(['htmlContent' => $business_users]);
    }
    
    
}
