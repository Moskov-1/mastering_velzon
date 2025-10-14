<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SystemUserController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
        // $this->middleware('can:user_create')->only(['create', 'store']);
    }

    public function index(Request $request){
        $users = User::where('is_admin_user', 1)->orderBy('id','desc')->get();
        if($request->ajax()){
            return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('status', function ($data) {
                $backgroundColor  = $data->status ? '#4CAF50' : '#ccc';
                $sliderTranslateX = $data->status ? '26px' : '2px';
                
                return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
            })
            ->addColumn('action', function ($data) {
                return '
                <button onclick="edit(' . $data->id . ')" type="button" class="btn btn-info btn-sm">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger btn-sm del">
                    <i class="mdi mdi-delete"></i>
                </button>
            ';
            })
            ->rawColumns([ 'status', 'action'])
            ->make(true);
        }
        return view('backend.layout.users.system_users.index');
    }
    public function create(){
        return view('backend.layout.users.system_users.form');
    }
    public function store(UserRequest $request){
        // dd($request->all());
        $data = $request->validated();
        // dd($data);
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_admin_user = $data['is_admin_user'];
        $user->password = bcrypt($data['password']);
        $user->role = User::roles()['ADMIN'];
        $user->save();
        
        return redirect()->route('backend.system-user.index')->with('success','System User Successfully created');
    }

    public function edit(User $system_user){
        
        return view('backend.layout.users.system_users.form', compact('system_user'));
    }

    
    public function update(Request $request, User $system_user){
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            // 'email'=> 'required|email',
            'password' => [['nullable', new PasswordRule]],
        ]);
        try {
            if(!is_null($request['password'])){
                $system_user->password = bcrypt($request['password']);
                $system_user->update();
            }
            $data = $request->only(['name','email']);
            $system_user->update($data);
            
        } catch (\Exception $e) {
            return redirect()->route('backend.system-user.index')->with('error','System User Failed to Update,,,'.$e->getMessage());
        }
        return redirect()->route('backend.system-user.index')->with('success','System User Successfully created');
    }

    public function status($id){
        try {
            $system_user = User::find($id);
            $system_user->status = !$system_user->status;
            $system_user->update();

            return response()->json(['status'=> 'success', 'message', 'Status Changed Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status'=> 'error', 'message', 'Status Change Failed ...'. $e->getMessage() ]);

        }
        
    }
    public function destroy(User $system_user){
        try {
            if($system_user->id == Auth::user()->id){
                return response()->json(['status'=> 'error', 'message', 'Can\'t delete own id ...']);
            }
            $system_user->delete();
        } catch (\Exception $e) {
            return response()->json(['status'=> 'error', 'message', 'User delete Failed ...'. $e->getMessage() ]);
        }
        return response()->json(['status'=> 'success', 'message', 'User deleted Successfully']);
    }
}
