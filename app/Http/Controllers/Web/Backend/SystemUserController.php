<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
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
            ->addColumn('roles', function ($user) {
                return $user->getRoleNames()
                    ->map(fn($role) => "<span class='badge bg-primary'>$role</span>")
                    ->implode(' ');
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
            ->rawColumns(['roles', 'status', 'action'])
            ->make(true);
        }
        return view('backend.layout.users.system_users.index');
    }
    public function create(){
        $roles = Role::all()->pluck('name')->toArray();
        return view('backend.layout.users.system_users.form', compact('roles'));
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
        $user->save();
        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }
        return redirect()->route('backend.system-user.index')->with('success','System User Successfully created');
    }

    public function edit(User $system_user){
        // dd($system_user->roles());
        $roles = Role::all()->pluck('name')->toArray();
        $userRoles = $system_user->getRoleNames()->toArray();
        // dd($system_user->getRoleNames());
        // dd([
        //     'permissions' => $system_user->getAllPermissions()->pluck('name'),
        //     'can_role_management' => $system_user->can('role_management'),
        //     'guard' => $system_user->guard_name ?? 'web (default)',
        //     Permission::pluck('guard_name', 'name'),
        //     Role::pluck('guard_name', 'name')
        // ]);
        // dd([
        //     'user_roles' => $system_user->getRoleNames(),
        //     'role_permissions' => $system_user->roles->flatMap->permissions->pluck('name')->unique(),
        //     'direct_permissions' => $system_user->permissions->pluck('name'),
        // ]);
        dd([
            'auth_guard' => auth()->getDefaultDriver(),
            'user_guard' => $system_user->guard_name ?? 'no guard column on users table',
        ]);
        return view('backend.layout.users.system_users.form', compact('system_user', 'roles', 'userRoles'));
    }

    
    public function update(Request $request, User $system_user){
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            // 'email'=> 'required|email',
            'password' => [['nullable', new PasswordRule]],
        ]);
        try {
            if(is_null($request['password'])){
                $system_user->password = bcrypt($request['password']);
                $system_user->update();
            }
            $data = $request->only(['name','email']);
            $system_user->update($data);
     
            if ($request->has('role')) {
                $system_user->syncRoles($request->role);
            }
            
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
            $system_user->delete();
        } catch (\Exception $e) {
            return response()->json(['status'=> 'error', 'message', 'User delete Failed ...'. $e->getMessage() ]);
        }
        return response()->json(['status'=> 'success', 'message', 'User deleted Successfully']);
    }
}
