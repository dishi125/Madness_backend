<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $page = "Dashboard";

    public function index(){
        return view('admin.dashboard')->with('page',$this->page);
    }

}
