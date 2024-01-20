<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    private $page = "Suggestions";

    public function index(){
        return view('admin.suggestions.list')->with('page',$this->page);
    }

    public function allSuggestionslist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'user',
                2 => 'message',
                3 => 'created_at',
            );
            $totalData = Suggestion::count();
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
                $Suggestions = Suggestion::with('user')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Suggestions = Suggestion::with('user')
                    ->where(function($query) use($search){
                        $query->where('message','LIKE',"%{$search}%")
                            ->orWhereHas('user',function ($mainQuery) use($search) {
                                $mainQuery->where('full_name', 'Like', '%' . $search . '%');
                            });
                        })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Suggestions->toArray());
            }

            $data = array();

            if(!empty($Suggestions))
            {
                foreach ($Suggestions as $Suggestion)
                {
                    $page_id = ProjectPage::where('route_url','admin.suggestions.list')->pluck('id')->first();

                    $nestedData['user'] = $Suggestion->user->full_name;
                    $nestedData['message'] = $Suggestion->message;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Suggestion->created_at));
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

}
