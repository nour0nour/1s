<?php
namespace Modules\User\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\AdminController;
use Modules\User\Events\VendorApproved;
use Modules\User\Models\Role;
use Modules\User\Models\User;
use Modules\Vendor\Models\VendorRequest;
use Modules\User\Exports\UserExport;

class UserController extends AdminController
{
    private Role $role;

    public function __construct(Role $role)
    {
        $this->setActiveMenu(route('user.admin.index'));
        $this->role = $role;
    }

    public function index(Request $request)
    {
        $this->checkPermission('user_view');
        $username = $request->query('s');
        $listUser = User::query()->orderBy('id','desc');
        if (!empty($username)) {
             $listUser->where(function($query) use($username){
                 $query->where('first_name', 'LIKE', '%' . $username . '%');
                 $query->orWhere('business_name', 'LIKE', '%' . $username . '%');
                 $query->orWhere('id',  $username);
                 $query->orWhere('phone',  $username);
                 $query->orWhere('email', 'LIKE', '%' . $username . '%');
                 $query->orWhere('last_name', 'LIKE', '%' . $username . '%');
                 $query->orWhere(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', '%' . $username . '%');
             });
        }
        if($request->query('role')){
            $listUser->role($request->query('role'));
        }
        //$listUser->with(['wallet']);
        $data = [
            'rows' => $listUser->paginate(20),
            'roles' => Role::all()
        ];
        return view('User::admin.index', $data);
    }

    public function create(Request $request)
    {

        $row = new \Modules\User\Models\User();
        $data = [
            'row' => $row,
            'roles' => Role::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("Users"),
                    'url'=>route('user.admin.index')
                ]
            ]
        ];
        return view('User::admin.detail', $data);
    }

    public function edit(Request $request, $id)
    {
        $row = User::find($id);
        if (empty($row)) {
            return redirect(route('user.admin.index'));
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermission('user_update')) {
            abort(403);
        }
        $data = [
            'row'   => $row,
            'roles' => Role::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("Users"),
                    'url'=>route('user.admin.index')
                ],
                [
                    'name'=>__("Edit User: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('User::admin.detail', $data);
    }

    public function password(Request $request,$id){

        $row = User::find($id);
        $data  = [
            'row'=>$row,
            'currentUser'=>Auth::user()
        ];
        if (empty($row)) {
            return redirect(route('user.admin.index'));
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermission('user_update')) {
            abort(403);
        }
        return view('User::admin.password',$data);
    }

    public function changepass(Request $request, $id)
    {
        if(is_demo_mode()){
            return redirect()->back()->with("error", __("DEMO MODE: You can not change password!"));
        }
        $rules = [];
        $urow = User::find($id);
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermission('user_update')) {
            abort(403);
        }
        $request->validate([
            'password'              => 'required|min:6|max:255|confirmed',
        ]);
        $password = $request->input('password');
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermission('user_update')) {
            if ($password) {
                if ($urow->id != Auth::user()->id) {
                    $rules['old_password'] = 'required';
                }
                $rules['password'] = 'required|string|min:6|confirmed';
            }
            $this->validate($request, $rules);
            if ($password) {
                if (!(Hash::check($request->input('old_password'), $urow->password))) {
                    // The Old passwords matches
                    return redirect()->back()->with("error", __("Your current password does not matches with the password you provided. Please try again."));
                }
            }
        }
        $urow->password = bcrypt($password);
        $urow->setRememberToken(Str::random(60));
        if ($urow->save()) {

            if ($request->input('role_id') and $role = $this->role->findById($request->input('role_id'))) {
                $urow->assignRole($role);
            }
            return redirect()->back()->with('success', __('Password updated!'));
        }
    }

    public function store(Request $request, $id)
    {
        if(is_demo_mode()){
            return back()->with('danger',  __('DEMO Mode: You can not do this') );
        }

        if($id and $id>0){
            $this->checkPermission('user_update');
            $row = User::find($id);
            if(empty($row)){
                abort(404);
            }
            if ($row->id != Auth::user()->id and !Auth::user()->hasPermission('user_update')) {
                abort(403);
            }

        }else{
            $this->checkPermission('user_create');
            $row = new User();
        }

        $rules = [
            'first_name'              => 'required|max:255',
            'last_name'              => 'required|max:255',
            'business_name'              => 'required|max:255',
            'status'              => 'required|max:50',
            'role_id'              => 'required|max:11',
            'email'              =>[
                'required',
                'email',
                'max:255',
                $id > 0 ? Rule::unique('users')->ignore($row->id) : Rule::unique('users')
            ],
            'user_name'=> [
                'required',
                'max:255',
                'min:4',
                'string',
                'alpha_dash',
                $id > 0 ? Rule::unique('users')->ignore($row->id) : Rule::unique('users')
            ],
        ];

        $request->validate($rules,[
            'business_name.required'=>__("Display name is a required field")
        ]);

        $data = [
            'first_name'=>$request->input('first_name'),
            'last_name'=>$request->input('last_name'),
            'user_name'=>$request->input('user_name'),
            'phone'=>$request->input('phone'),
            'birthday'=>$request->input('birthday') ? date("Y-m-d", strtotime($request->input('birthday'))) : null,
            'bio'=>$request->input('bio'),
            'status'=>$request->input('status'),
            'avatar_id'=>$request->input('avatar_id'),
            'email'=>$request->input('email'),
            'business_name'=>$request->input('business_name'),
            'name'=>$request->input('name'),
            'address'=>$request->input('address'),
            'address2'=>$request->input('address2'),
            'country'=>$request->input('country'),
            'city'=>$request->input('city'),
            'state'=>$request->input('state'),
            'zip_code'=>$request->input('zip_code'),
            'vendor_commission_type'=>$request->input('vendor_commission_type'),
            'vendor_commission_amount'=>$request->input('vendor_commission_amount'),
        ];
        $row->role_id = $request->input('role_id');
        if($request->input('is_email_verified')){
            if(!$row->email_verified_at) $row->email_verified_at = date('Y-m-d H:i:s');
        }else{
            $row->email_verified_at = null;
        }

        $row->fillByAttr(array_keys($data),$data);

        //Block all service when user is block
        if($row->status == "blocked"){
            $services = get_bookable_services();
            if(!empty($services)){
                foreach ($services as $service){
                    $service::query()->where("create_user",$row->id)->update(['status' => "draft"]);
                }
            }
        }

        if ($row->save()) {
            return back()->with('success', ($id and $id>0) ? __('User updated'):__("User created"));
        }
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');
        if ($pre_selected && $selected) {
            if (is_array($selected)) {
                $res = User::whereIn('id', $selected)->take(50)->get();
                $items = [];
                if(!empty($res)){
                    foreach ($res as $item){
                        $items[] = [
                            'id'=>$item->id,
                            'text'=>$item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')'
                        ];
                    }
                }
            } else {
                $items = [];
                $item = User::find($selected);
                if(!empty($item)){
                    $items[] = [
                        'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')'
                    ];
                }
            }

            return [
                'results'=>$items
            ];
        }

        $q = $request->query('q');
        $query = User::select('*');
        if ($q) {
            $query->where(function ($query) use ($q) {
                $query->where('first_name', 'like', '%' . $q . '%')->orWhere('last_name', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%')->orWhere('id', $q)->orWhere('phone', 'like', '%' . $q . '%');
            });
        }
        $res = $query->orderBy('id', 'desc')->orderBy('first_name', 'asc')->limit(100)->get();
        $data = [];
        if (!empty($res)) {
            if($request->query("user_type") == "vendor"){
                //for only vendor
                foreach ($res as $item) {
                    if($item->hasPermission("dashboard_vendor_access")){
                        $data[] = [
                            'id'   => $item->id,
                            'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')',
                        ];
                    }
                }
            }else{
                //for all
                foreach ($res as $item) {
                    $data[] = [
                        'id'   => $item->id,
                        'text' => $item->getDisplayName() ? $item->getDisplayName() . ' (#' . $item->id . ')' : $item->email . ' (#' . $item->id . ')',
                    ];
                }
            }
        }
        return response()->json([
            'results' => $data
        ]);
    }

    public function bulkEdit(Request $request)
    {
        if(is_demo_mode()){
            return redirect()->back()->with("error","DEMO MODE: You are not allowed to do it");
        }
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));
        if ($action == 'delete') {
            foreach ($ids as $id) {
                if($id == Auth::id()) continue;
                $query = User::where("id", $id)->first();
                if(!empty($query)){
                    $query->email.='_d_'.uniqid().rand(0,99999);
                    $query->save();
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                User::where("id", $id)->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }
    public function userUpgradeRequest(Request $request)
    {
        $this->checkPermission('user_view');
        $listUser = VendorRequest::query();
        $data = [
            'rows' => $listUser->whereHas('user')->with(['user','role','approvedBy'])->orderBy('id','desc')->paginate(20),
            'roles' => Role::all(),

        ];
        return view('User::admin.upgrade-user', $data);
    }
    public function userUpgradeRequestApproved(Request $request)
    {
        $this->checkPermission('user_create');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at leas 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));

        switch ($action){
            case "delete":
                foreach ($ids as $id) {
                    $query = VendorRequest::find( $id);
                    if(!empty($query)){
                        $query->delete();
                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            default:
                foreach ($ids as $id) {
                    $vendorRequest = VendorRequest::find( $id);
                    if(!empty($vendorRequest)){
                        $vendorRequest->update(['status' => $action,'approved_time'=>now(),'approved_by'=>Auth::id()]);
                        $user = User::find($vendorRequest->user_id);
                        if(!empty($user)){
                            $user->assignRole($vendorRequest->role_request);
                        }
                        event(new VendorApproved($user,$vendorRequest));
                    }
                }
                return redirect()->back()->with('success', __('Updated successfully!'));
                break;
        }
    }
    public function userUpgradeRequestApprovedId(Request $request, $id)
    {
        $this->checkPermission('user_create');
        if (empty($id))
            return redirect()->back()->with('error', __('Select at least 1 item!'));

        $vendorRequest = VendorRequest::find( $id);
        if(!empty($vendorRequest)){
            $vendorRequest->update(['status' => 'approved','approved_time'=>now(),'approved_by'=>Auth::id()]);
            $user = User::find($vendorRequest->user_id);
            if(!empty($user)){
                $user->assignRole($vendorRequest->role_request);
            }

            event(new VendorApproved($user,$vendorRequest));
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

    public function export()
    {
        $this->checkPermission('user_view');
        return (new UserExport())->download('user-' . date('M-d-Y') . '.xlsx');
    }
    public function verifyEmail(Request $request,$id)
    {
        $user = User::find($id);
        if(!empty($user)){
            $user->email_verified_at = now();
            $user->save();
            return redirect()->back()->with('success', __('Verify email successfully!'));
        }else{
            return redirect()->back()->with('error', __('Verify email cancel!'));
        }
    }

}
