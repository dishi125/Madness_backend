<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $page = "Madness Mart";

    public function index()
    {
        return view('admin.auth.login');
    }

    public function invalid_page()
    {
        return view('admin.403_page')->with('page',$this->page);
    }

    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $user = User::where('email',$request->email)->where('decrypted_password',$request->password)->where('estatus',1)->first();
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials) && $user) {
//            dd(Auth::user()->toArray());
            return response()->json(['status'=>200]);
            /*return redirect()->intended('admin/dashboard')
                ->withSuccess('You have Successfully loggedin');*/
        }
        return response()->json(['status'=>400]);
//        return redirect("admin")->withSuccess('Oppes! You have entered invalid credentials');
    }

    /*public function dashboard()
    {
        if(Auth::check()){
            return view('admin.dashboard');
        }

        return redirect("admin")->withSuccess('Opps! You do not have access');
    }*/

    public function logout() {
        Session::flush();
        Auth::logout();

        return Redirect('admin');
    }
}
