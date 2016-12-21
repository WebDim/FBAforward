<?php

namespace App\Http\Controllers\Admin;

use App\Package;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;

class UsersController extends Controller
{

    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::lists('name', 'id');

        $packages = Package::active()->lists('name', 'id');

        $job_titles = getSetting('JOB_TITLES');

        return view('admin.users.create_edit')->with(compact('roles', 'job_titles', 'packages'));
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {

        $account = [];

        $avatar = 'avatar.png';

        if ($request->hasFile('avatar')) {
            $destinationPath = public_path() . '/uploads/avatars';

            $avatar = hash('sha256', mt_rand()) . '.' . $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move($destinationPath, $avatar);

            \Image::make(asset('uploads/avatars/' . $avatar))->fit(300, null, null, 'top-left')->save('uploads/avatars/' . $avatar);
        }

        $account['avatar'] = $avatar;

        $account['name'] = $request->input('name');
        $valid_package = true;
        if ($request->input('package_id')) {
            if ($request->input('package_id') == getSetting('DEFAULT_PACKAGE_ID')) {
                $account['package_id'] = getSetting('DEFAULT_PACKAGE_ID');
            } else {
                $valid_package = false;
            }
        }
        $account['email'] = $request->input('email');
        $account['mobile'] = $request->input('mobile');
        $account['job_title'] = $request->input('job_title');
        $account['address'] = $request->input('address');
        $account['role_id'] = $request->input('role');

        $user = new User($account);

        $user->password = bcrypt($request->input('password'));

        $user->save();

        $package_message = $valid_package ? '' : ' Please Note you can\'t add package without Stripe Subscription';

        $status_message = $user->name . ' has been added Successfully.' . $package_message;

        return redirect('admin/users')->with('success', $status_message);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function show(User $user)
    {
        return view('admin.users.show')->with(compact('user'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function edit(User $user)
    {
        $roles = Role::lists('name', 'id');

        $packages = Package::active()->lists('name', 'id');

        $job_titles = getSetting('JOB_TITLES');

        return view('admin.users.create_edit')->with(compact('user', 'roles', 'job_titles', 'packages'));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {

        if ($request->hasFile('avatar')) {
            $destinationPath = public_path() . '/uploads/avatars';

            if ($user->avatar != "uploads/avatars/avatar.png") {
                @unlink($user->avatar);
            }

            $avatar = hash('sha256', mt_rand()) . '.' . $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move($destinationPath, $avatar);

            \Image::make(asset('uploads/avatars/' . $avatar))->fit(300, null, null, 'top-left')->save('uploads/avatars/' . $avatar);

            $user->avatar = $avatar;
        }

        $user->name = $request->input('name');

        $valid_package = true;

        if ($request->input('package_id')) {
            if ($request->input('package_id') != getSetting('DEFAULT_PACKAGE_ID')) {
                /**
                 * check if the user subscribed and new package selected
                 */
                if ($user->subscribed('MEMBERSHIP')) {
                    /**
                     * Swap subscription plan
                     */
                    if ($request->input('package_id') != $user->package_id) {
                        $package = Package::find($request->input('package_id'));
                        $user->subscription('MEMBERSHIP')->swap($package->plan);
                        $user->package_id = $request->input('package_id');
                    }
                } else {
                    /**
                     * can't add package without Stripe Subscription
                     */
                    $valid_package = false;
                }
            } else {
                /**
                 * Assign Default free package to user
                 */
                $user->package_id = getSetting('DEFAULT_PACKAGE_ID');
                if ($user->subscribed('MEMBERSHIP')) {
                    $user->subscription('MEMBERSHIP')->cancel();
                }
            }
        } else {
            /**
             * reset user package
             */
            $user->package_id = 0;
        }

        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->job_title = $request->input('job_title');
        $user->address = $request->input('address');
        $user->updated_at = \Carbon::now();
        $user->role_id = $request->input('role');

        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        $package_message = $valid_package ? '' : ' Please Note you can\'t add package without Stripe Subscription';

        $status_message = $user->name . ' has been updated Successfully.' . $package_message;

        return redirect('admin/users')->with('success', $status_message);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public
    function destroy(Request $request, User $user)
    {
        if ($request->ajax()) {
            $user->delete();
            return response()->json(['success' => 'User has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }

}
