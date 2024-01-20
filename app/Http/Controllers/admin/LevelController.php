<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    private $page = "Levels";

    public function index(){
        return view('admin.levels.list')->with('page',$this->page);
    }

    public function addorupdateLevel(Request $request){
        $messages = [
            'commission_percentage.required' =>'Please Provide a Commission Percentage',
            'no_child_users.required' =>'Please Provide a Number of Child Users',
        ];

        $validator = Validator::make($request->all(), [
            'commission_percentage' => 'required',
            'no_child_users' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $Level = Level::find($request->level_id);

            if(!$Level){
                return response()->json(['status' => '400']);
            }

            $Level->commission_percentage = $request->commission_percentage;
            $Level->no_child_users = $request->no_child_users;
        }
        else{
            $action = "add";
            $Level = new Level();
            $Level->title = $request->title;
            $Level->commission_percentage = $request->commission_percentage;
            $Level->no_child_users = $request->no_child_users;
            $Level->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }

        $Level->save();

        return response()->json(['status' => '200', 'action' => $action, 'level' => getLastLevel() + 1]);
    }

    public function allLevelList(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'id',
                1 =>'title',
                2=> 'commission_percentage',
                3=> 'no_child_users',
                4=> 'created_at',
                5=> 'action',
            );
            $totalData = Level::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $Levels = Level::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Levels = Level::where(function($query) use($search){
                        $query->where('title','LIKE',"%{$search}%")
                            ->orWhere('commission_percentage', 'LIKE',"%{$search}%")
                            ->orWhere('no_child_users', 'LIKE',"%{$search}%");
                        })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                $totalFiltered = Level::where(function($query) use($search){
                        $query->where('title','LIKE',"%{$search}%")
                            ->orWhere('commission_percentage', 'LIKE',"%{$search}%")
                            ->orWhere('no_child_users', 'LIKE',"%{$search}%");
                        })
                        ->count();
            }

            $data = array();

            if(!empty($Levels))
            {
                foreach ($Levels as $Level)
                {
                    $page_id = ProjectPage::where('route_url','admin.levels.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editLevelBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#LevelModal" onclick="" data-id="' .$Level->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }

                    $nestedData['title'] = $Level->title;
                    $nestedData['commission_percentage'] = $Level->commission_percentage;
                    $nestedData['no_child_users'] = $Level->no_child_users;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Level->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

    public function editLevel($id){
        $Level = Level::find($id);
        return response()->json($Level);
    }
}
