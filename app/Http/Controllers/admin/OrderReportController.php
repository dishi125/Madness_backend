<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ProjectPage;
use App\Models\User;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    private $page = "Order Report";

    public function index(){
        $users = User::where('role',3)->get();
        return view('admin.order_report.list',compact('users'))->with('page',$this->page);
    }

    public function allOrderReportlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'id',
                1 =>'order_info',
                2=> 'customer_info',
                3=> 'note',
                4=> 'payment_status',
                5=> 'order_status',
                6=> 'created_at',
            );

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            $totalData = Order::count();
            $totalFiltered = $totalData;

            if(empty($request->input('search.value')))
            {
                $Orders = Order::with('order_item');
                if (isset($request->user_id_filter) && $request->user_id_filter!=""){
                    $Orders = $Orders->where('user_id',$request->user_id_filter);
                }
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $Orders = $Orders->whereRaw("DATE(created_at) between '".$request->start_date."' and '".$request->end_date."'");
                }
                $Orders = $Orders->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Orders->toArray());
            }
            else {
                $search = $request->input('search.value');
                $Orders = Order::with('order_item');
                if (isset($request->user_id_filter) && $request->user_id_filter!=""){
                    $Orders = $Orders->where('user_id',$request->user_id_filter);
                }
                if (isset($request->start_date) && $request->start_date!="" && isset($request->end_date) && $request->end_date!=""){
                    $Orders = $Orders->whereRaw("DATE(created_at) between '".$request->start_date."' and '".$request->end_date."'");
                }
                $Orders = $Orders->where(function($query) use($search){
                    $query->where('custom_orderid','LIKE',"%{$search}%")
                        ->orWhere('payment_type', 'LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Orders->toArray());
            }

            $data = array();

            if(!empty($Orders))
            {
                foreach ($Orders as $Order)
                {
                    $page_id = ProjectPage::where('route_url','admin.order_report.list')->pluck('id')->first();

                    $order_info = '<span>Order ID: '.$Order->custom_orderid.'</span>';
                    $order_info .= '<span>Total Order Cost: <i class="fa fa-inr" aria-hidden="true"></i> '.$Order->total_ordercost.'</span>';
                    $order_info .= '<span>Total Items: '.count($Order->order_item).'</span>';

                    $delivery_address = json_decode($Order->delivery_address,true);
                    $customer_info = $delivery_address['CustomerName'].'<span><i class="fa fa-phone" aria-hidden="true"></i> '.$delivery_address['CustomerMobile'].'</span><span><i class="fa fa-map-marker" aria-hidden="true"></i>
 '.$delivery_address['DelAddress1'].'</span>';

                    $NoteBoxDisplay = $Order->order_note;

                    if(isset($Order->payment_status)) {
                        $payment_status = getPaymentStatus($Order->payment_status);
                        $payment_type = getPaymentType($Order->payment_type);
                        $payment_status = '<span class="'.$payment_status['class'].'">'.$payment_status['payment_status'].'</span><span>'.$payment_type.'</span>';
                    }

                    if(isset($Order->order_status)) {
                        $order_status = getOrderStatus($Order->order_status);
                        $order_status = '<span class="'.$order_status['class'].'">'.$order_status['order_status'].'</span>';
                    }

                    $date = '<span><b>Order Date:</b></span><span>'.date('d-m-Y h:i A', strtotime($Order->created_at)).'</span>';
                    if(isset($Order->delivery_date)){
                        $date .= '<span><b>Delivery Date:</b></span><span>'.$Order->delivery_date.'</span>';
                    }

                    $table = '<table class="subclass text-center" cellpadding="6" cellspacing="0" border="0" style="padding-left:50px; width: 50%">';
                    $item = 1;
                    foreach ($Order->order_item as $order_item){
                        $item_details = json_decode($order_item->item_details,true);
                        $ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();
                        $table .='<tr>';
                        if(isset($ProductVariant->variant_images)){
                            $table .='<td>'.$item.'</td><td class="multirow"><img src="'.url($ProductVariant->variant_images[0]).'" width="50px" height="50px"></td>';
                        }
                        $table .='<td class="multirow text-left">
                                    <b>'.$item_details['ProductTitle'].'</b>';
                        $table .='<span>'.$item_details['attribute'].': '.$item_details['attributeTerm'].'</span>';
                        $orderItemPrice = '';
                        if (isset($item_details['itemQuantity'])){
                            $orderItemPrice = ' &times; '.$item_details['itemQuantity'].' Qty';
                        }
                        if (isset($item_details['orderItemPrice'])){
                            // $table .= '<td>Price: <i class="fa fa-inr" aria-hidden="true"></i> '.$item_details['orderItemPrice'].'</td>';
                            $table .= '<td class="multirow text-right">Item Price: <i class="fa fa-inr" aria-hidden="true"></i> '.$item_details['orderItemPrice'].$orderItemPrice;
                        }
                        if (isset($item_details['SubDiscount'])){
                            $table .= '<span>Sub Discount: <i class="fa fa-inr" aria-hidden="true"></i> '.$item_details['SubDiscount'].'</span>';
                        }
                        if (isset($item_details['totalItemAmount'])){
                            $table .= '<span>total Amount: <i class="fa fa-inr" aria-hidden="true"></i> '.$item_details['totalItemAmount'].'</span>';
                        }
                        if (isset($item_details['itemPayableAmt'])){
                            $table .= '<span>Payable Amount: <i class="fa fa-inr" aria-hidden="true"></i> '.$item_details['itemPayableAmt'].'</span></td>';
                        }
                        $table .= '</tr>';
                        $item++;
                    }
                    $table .='</table>';

                    $nestedData['order_info'] = $order_info;
                    $nestedData['customer_info'] = $customer_info;
                    $nestedData['note'] = $NoteBoxDisplay;
                    $nestedData['payment_status'] = $payment_status;
                    $nestedData['order_status'] = $order_status;
                    $nestedData['created_at'] = $date;
                    $nestedData['table1'] = $table;
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
