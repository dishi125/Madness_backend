<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\MonthlyCommission;
use App\Models\ProjectPage;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    private $page = "Monthly Commission";

    public function index(){
        return view('admin.monthly_commissions.list')->with('page',$this->page);
    }

    public function allMonthlyCommissionlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'id',
                1 =>'user_info',
                2=> 'total_amount',
                3=> 'commission_status',
                4=> 'payment_date',
                5=> 'month',
                6=> 'action',
            );

            $tab_type = $request->tab_type;
            if ($tab_type == "Pending_monthly_commission_tab"){
                $commission_status = [1];
            }
            elseif ($tab_type == "Success_monthly_commission_tab"){
                $commission_status = [2];
            }
            elseif ($tab_type == "OnHold_monthly_commission_tab"){
                $commission_status = [3];
            }
            elseif ($tab_type == "Cancelled_monthly_commission_tab"){
                $commission_status = [4];
            }
            elseif ($tab_type == "Failed_monthly_commission_tab"){
                $commission_status = [5];
            }

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            $totalData = MonthlyCommission::count();
            if (isset($commission_status)){
                $totalData = MonthlyCommission::whereIn('commission_status',$commission_status)->count();
            }
            $totalFiltered = $totalData;

            if(empty($request->input('search.value')))
            {
                $MonthlyCommissions = MonthlyCommission::with('commission','user');
                if (isset($commission_status)){
                    $MonthlyCommissions = $MonthlyCommissions->whereIn('commission_status',$commission_status);
                }
                $MonthlyCommissions = $MonthlyCommissions->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $MonthlyCommissions = MonthlyCommission::with('commission','user');
                if (isset($commission_status)){
                    $MonthlyCommissions = $MonthlyCommissions->whereIn('commission_status',$commission_status);
                }
                $MonthlyCommissions = $MonthlyCommissions->where(function($mainQuery) use($search){
                    $mainQuery->where('total_amount','LIKE',"%{$search}%")
                        ->orWhere('current_month', 'LIKE',"%{$search}%")
                        ->orWhere('current_year', 'LIKE',"%{$search}%")
                        ->orWhere('payment_date', 'LIKE',"%{$search}%")
                        ->orWhereHas('user',function ($Query) use($search) {
                            $Query->where('first_name', 'Like', '%' . $search . '%')->orWhere('last_name', 'Like', '%' . $search . '%');
                        });
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($MonthlyCommissions->toArray());
            }

            $data = array();

            if(!empty($MonthlyCommissions))
            {
                foreach ($MonthlyCommissions as $MonthlyCommission)
                {
                    $page_id = ProjectPage::where('route_url','admin.monthly_commissions.list')->pluck('id')->first();
                    if(isset($MonthlyCommission->user->profile_pic) && $MonthlyCommission->user->profile_pic!=null){
                        $profile_pic = url('public/images/profile_pic/'.$MonthlyCommission->user->profile_pic);
                    }
                    else{
                        $profile_pic = url('public/images/default_avatar.jpg');
                    }

                    $user_info = '<img src="'.$profile_pic.'" width="50px" height="50px">';
                    if (isset($MonthlyCommission->user->first_name) && isset($MonthlyCommission->user->last_name)){
                        $user_info .= '<span>'.$MonthlyCommission->user->first_name.' '.$MonthlyCommission->user->last_name.'</span>';
                    }

                    if(isset($MonthlyCommission->commission_status)) {
                        $commission_status = getCommissionStatus($MonthlyCommission->commission_status);
                        $commission_status = '<span class="'.$commission_status['class'].'">'.$commission_status['commission_status'].'</span>';
                    }

                    $payment_date = "";
                    if (isset($MonthlyCommission->payment_date)){
                        $payment_date = date('d-m-Y H:i:s', strtotime($MonthlyCommission->payment_date));
                    }

                    $action = '';
                    $action .= '<button class="btn gradient-9 btn-sm ViewMonthlyCommissionBtn" data-id="'.$MonthlyCommission->id.'"><i class="fa fa-eye" aria-hidden="true"></i></button>';
                    if ( (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) && $MonthlyCommission->commission_status==1 ){
                        $action .= '<button id="PayCommissionBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#PayCommissionModal" onclick="" data-id="' .$MonthlyCommission->id. '">Pay</button>';
                    }
                    $nestedData['user_info'] = $user_info;
                    $nestedData['total_amount'] = '<i class="fa fa-inr" aria-hidden="true"></i> '.$MonthlyCommission->total_amount;
                    $nestedData['commission_status'] = $commission_status;
                    $nestedData['payment_date'] = $payment_date;
                    $nestedData['month'] = date("F", mktime(0, 0, 0, $MonthlyCommission->current_month, 1)) .", ".$MonthlyCommission->current_year;
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

//            return json_encode($json_data);
            echo json_encode($json_data);
        }
    }

    public function viewMonthlyCommission($id){
        return view('admin.monthly_commissions.view',compact('id'))->with('page',$this->page);
    }

    public function allCommissionlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'id',
                1 =>'order_info',
                2 =>'commission_info',
                3=> 'order_by',
                4=> 'order_status',
                5=> 'date',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            $monthly_commission_id = $request->id;

            $totalData = Commission::where('monthly_commission_id',$monthly_commission_id)->count();
            $totalFiltered = $totalData;

            if(empty($request->input('search.value')))
            {
                $Commissions = Commission::with('order.user','order.order_item','level')
                    ->where('monthly_commission_id',$monthly_commission_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Commissions = Commission::with('order.user','order.order_item','level')
                    ->where('monthly_commission_id',$monthly_commission_id)
                    ->where(function($mainQuery) use($search){
                        $mainQuery->where('amount','LIKE',"%{$search}%")
                            ->orWhereHas('order',function ($Query) use($search) {
                                $Query->where('custom_orderid', 'Like', '%' . $search . '%')->orWhere('total_ordercost', 'Like', '%' . $search . '%')->orWhere('order_status', 'Like', '%' . $search . '%')->orWhere('created_at', 'Like', '%' . $search . '%')->orWhere('delivery_date', 'Like', '%' . $search . '%');
                            })
                            ->orWhereHas('order.user',function ($Query) use($search) {
                                $Query->where('first_name', 'Like', '%' . $search . '%')->orWhere('last_name', 'Like', '%' . $search . '%');
                            })
                            ->orWhereHas('level',function ($Query) use($search) {
                                $Query->where('title', 'Like', '%' . $search . '%');
                            });
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Commissions->toArray());
            }

            $data = array();

            if(!empty($Commissions))
            {
                foreach ($Commissions as $Commission)
                {
                    $order_info = '<span>Order ID: '.$Commission->order->custom_orderid.'</span>';
                    $order_info .= '<span>Total Order Cost: <i class="fa fa-inr" aria-hidden="true"></i> '.$Commission->order->total_ordercost.'</span>';
                    $order_info .= '<span>Total Items: '.count($Commission->order->order_item).'</span>';

                    $order_by = '';
                    if (isset($Commission->order->user->first_name)){
                        $order_by .= $Commission->order->user->first_name . " ";
                    }
                    if (isset($Commission->order->user->last_name)){
                        $order_by .= $Commission->order->user->last_name;
                    }

                    if(isset($Commission->order->order_status)) {
                        $order_status = getOrderStatus($Commission->order->order_status);
                        $order_status = '<span class="'.$order_status['class'].'">'.$order_status['order_status'].'</span>';
                    }

                    $date = '<span><b>Order Date:</b></span><span>'.date('d-m-Y H:i:s', strtotime($Commission->order->created_at)).'</span>';
                    if(isset($Commission->order->delivery_date)){
                        $date .= '<span><b>Delivery Date:</b></span><span>'.date('d-m-Y H:i:s', strtotime($Commission->order->delivery_date)).'</span>';
                    }

                    $commission_info = $Commission->level->title;
                    $commission_info .= '<span><i class="fa fa-inr" aria-hidden="true"></i> '.$Commission->amount.'</span>';

                    $nestedData['order_info'] = $order_info;
                    $nestedData['commission_info'] = $commission_info;
                    $nestedData['order_by'] = $order_by;
                    $nestedData['order_status'] = $order_status;
                    $nestedData['date'] = $date;
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

    public function pay_commission($id){
        $monthly_commission = MonthlyCommission::where('id',$id)->first();
        if ($monthly_commission) {
            $monthly_commission->commission_status = 2;
            $monthly_commission->save();

            return ['status' => 200];
        }
        return ['status' => 400];
    }
}
