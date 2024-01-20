<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDropdown;
use App\Models\Faq;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    private $page = "FAQ";

    public function index(){
        $action = "list";
        return view('admin.faq.list',compact('action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        return view('admin.faq.list',compact('action'))->with('page',$this->page);
    }

    public function save(Request $request){
        $messages = [
            'question.required' =>'Please provide a Question',
            'answer.required' =>'Please provide a Answer',
        ];

        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $Faq = Faq::find($request->faq_id);

            if(!$Faq){
                return response()->json(['status' => '400']);
            }
        }
        else{
            $action = "add";
            $Faq = new Faq();
        }
        $Faq->question = $request->question;
        $Faq->answer = $request->answer;
        $Faq->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allFaqlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'question',
                2 => 'answer',
                3 => 'created_at',
                4 => 'action',
            );
            $totalData = Faq::count();
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
                $Faqs = Faq::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Faqs = Faq::where(function($query) use($search){
                        $query->where('question','LIKE',"%{$search}%")
                                ->orWhere('answer','LIKE',"%{$search}%");
                        })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                $totalFiltered = count($Faqs->toArray());
            }

            $data = array();

            if(!empty($Faqs))
            {
                foreach ($Faqs as $Faq)
                {
                    $page_id = ProjectPage::where('route_url','admin.faq.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editFaqBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$Faq->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteFaqBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteFaqModal" onclick="" data-id="' .$Faq->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['question'] = $Faq->question;
                    $nestedData['answer'] = html_entity_decode($Faq->answer);
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Faq->created_at));
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

    public function edit($id){
        $action = "edit";
        $Faq = Faq::find($id);

        return view('admin.faq.list',compact('action','Faq'))->with('page',$this->page);
    }

    public function delete($id){
        $Faq = Faq::find($id);
        if ($Faq){
            $Faq->estatus = 3;
            $Faq->save();
            $Faq->delete();

            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

}
