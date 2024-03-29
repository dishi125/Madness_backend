<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeTerm;
use App\Models\Category;
use App\Models\Commission;
use App\Models\MonthlyCommission;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class OrderController extends Controller
{
    private $page = "Orders";

    public function index(){
        return view('admin.orders.list')->with('page',$this->page);
    }

    public function allOrderlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'id',
                1 =>'order_info',
                2=> 'customer_info',
                3=> 'note',
                4=> 'payment_status',
                5=> 'order_status',
                6=> 'created_at',
                7=> 'action',
            );

            $tab_type = $request->tab_type;
            if ($tab_type == "NewOrder_orders_tab"){
                $order_status = [1];
            }
            elseif ($tab_type == "OutforDelivery_orders_tab"){
                $order_status = [2];
            }
            elseif ($tab_type == "Delivered_orders_tab"){
                $order_status = [3];
            }
            elseif ($tab_type == "ReturnRequest_orders_tab"){
                $order_status = [4,5];
            }
            elseif ($tab_type == "Returned_orders_tab"){
                $order_status = [6];
            }
            elseif ($tab_type == "Cancelled_orders_tab"){
                $order_status = [7,8];
            }

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            $totalData = Order::count();
            if (isset($order_status)){
                $totalData = Order::whereIn('order_status',$order_status)->count();
            }
            $totalFiltered = $totalData;

            if(empty($request->input('search.value')))
            {
                $Orders = Order::with('order_item');
                if (isset($order_status)){
                    $Orders = $Orders->whereIn('order_status',$order_status);
                }
                $Orders = $Orders->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Orders = Order::with('order_item');
                if (isset($order_status)){
                    $Orders = $Orders->whereIn('order_status',$order_status);
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
                    $page_id = ProjectPage::where('route_url','admin.orders.list')->pluck('id')->first();

                    $action = '';
                    $action .= '<button id="invoiceBtn" class="btn btn-gray text-blue btn-sm" onclick="getInvoiceData(\''.$Order->id.'\')"><i class="fa fa-print" aria-hidden="true"></i></button>';

                    if( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ) {
                        $action .= '<button id="ViewOrderBtn" class="btn gradient-9 btn-sm" onclick="editOrder(' . $Order->id . ')"><i class="fa fa-eye" aria-hidden="true"></i></button>';
                    }
                    if ( isset($Order->order_status) && $Order->order_status == 4 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $action .= '<button type="button" class="btn mb-1 btn-success btn-xs" data-id="'.$Order->id.'" id="ApproveReturnRequestBtn">Approve</button>';
                        $action .= '<button type="button" class="btn mb-1 btn-danger btn-xs" data-id="'.$Order->id.'" id="RejectReturnRequestBtn">Reject</button>';
                    }

                    $order_info = '<span>Order ID: '.$Order->custom_orderid.'</span>';
                    $order_info .= '<span>Total Order Cost: <i class="fa fa-inr" aria-hidden="true"></i> '.$Order->total_ordercost.'</span>';
                    $order_info .= '<span>Total Items: '.count($Order->order_item).'</span>';

                    $delivery_address = json_decode($Order->delivery_address,true);
                    $customer_info = $delivery_address['CustomerName'].'<span><i class="fa fa-phone" aria-hidden="true"></i> '.$delivery_address['CustomerMobile'].'</span><span><i class="fa fa-map-marker" aria-hidden="true"></i>
 '.$delivery_address['DelAddress1'].'</span>';

                    $NoteBoxDisplay = $Order->order_note;
                    if( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ) {
                        $NoteBoxDisplay = '<textarea class="custom-textareaBox orderNoteBox" id="orderNoteBox' . $Order->id . '" rows="4" data-id="' . $Order->id . '">' . $Order->order_note . '</textarea>';
                    }

                    if(isset($Order->payment_status)) {
                        $payment_status = getPaymentStatus($Order->payment_status);
                        $payment_type = getPaymentType($Order->payment_type);
                        $payment_status = '<span class="'.$payment_status['class'].'">'.$payment_status['payment_status'].'</span><span>'.$payment_type.'</span>';
                    }

                    if(isset($Order->order_status)) {
                        $order_status = getOrderStatus($Order->order_status);
                        $order_status = '<span class="'.$order_status['class'].'">'.$order_status['order_status'].'</span>';
                    }

                    if ($Order->order_status == 4 || $Order->order_status == 6){
                        $returnreq_images = explode(",",$Order->order_return_imgs);
                        $returnreq_images_paths = array_map(function ($val){
                            return url('public/'.$val);
                        }, $returnreq_images);
                        $returnreq_images_paths = "['".implode("','",$returnreq_images_paths)."']";
                        $order_status .= '<span class="returnReqImgs" id="returnReqImgs_'.$Order->id.'">
                                <a href="javascript:void(0)" class="btn btn-dark btn-sm"><i class="fa fa-image"></i></a>
                                <script type="text/javascript">
                                    $("#returnReqImgs_'.$Order->id.'").slickLightbox({images: '.$returnreq_images_paths.'});
                                </script>
                                </span>';
                        $order_status .= '<button id="VideoBtn" class="btn btn-sm text-blue" data-id="'.$Order->id.'" data-toggle="modal" data-target="#ReturnReqVideoModal"><i class="fa fa-video-camera" aria-hidden="true"></i></button>';
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
                    $nestedData['action'] = $action;
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

//            return json_encode($json_data);
            echo json_encode($json_data);
        }
    }

    public function updateOrdernote(Request $request){
        $order = Order::find($request->orderid);
        if($order) {
            $order->order_note = $request->orderNote;
            $order->save();
            return ['status' => 200];
        }
        return ['status' => 400];
    }

    public function viewOrder($orderid){
        $Order = Order::with('order_item')->where('id',$orderid)->first();
        $delivery_address = json_decode($Order->delivery_address,true);
        return view('admin.orders.view',compact('Order','delivery_address'))->with('page',$this->page);
    }

    public function save(Request $request)
    {
//        $myValue = array();
//        parse_str($request['orderItemForm1'],$myValue);
//        dd($request->all(),$myValue);

        $delivery_address['CustomerName'] = $request->CustomerName;
        $delivery_address['CustomerMobile'] = $request->CustomerMobile;
        $delivery_address['DelAddress1'] = $request->DelAddress1;
        $delivery_address['DelAddress2'] = $request->DelAddress2;
        $delivery_address['Landmark'] = $request->Landmark;
        $delivery_address['City'] = $request->City;
        $delivery_address['State'] = $request->State;
        $delivery_address['Country'] = $request->Country;
        $delivery_address['Pincode'] = $request->Pincode;

        $Order = Order::with('order_item')->where('id',$request->order_id)->first();
        if(!$Order){
            return ['status' => 400];
        }
        $old_order_status = $Order->order_status;
//        $Order->order_status = $request->order_status;
        $Order->payment_status = $request->payment_status;
        $Order->delivery_address = json_encode($delivery_address);
        $Order->save();

        if ($old_order_status == 1 && ($request->order_status==2 || $request->order_status==8)){
            $Order->order_status = $request->order_status;

            foreach ($Order->order_item as $order_item){
                if ($order_item->order_status != 6 && $order_item->order_status != 7 && $order_item->order_status != 8) {
                    $order_item->order_status = $request->order_status;
                    //Minus item from Stock in case of out for delivery product
                    if ($order_item->order_status == 2){
                        $item_details = json_decode($order_item->item_details, true);
                        $product_variant = ProductVariant::where('id',$item_details['variantId'])->first();
                        $product_variant->total_orders = $product_variant->total_orders + 1;
                        if ($product_variant->stock > 0) {
                            $product_variant->stock = $product_variant->stock - $order_item->item_quantity;
                            $product_variant->save();
                        }
                    }

                    //For refund amount in case of Cancelled item
                    if ($request->order_status == 8 && $Order->payment_type == 1){
                        $Order->total_refund_amount = $Order->total_refund_amount + $order_item->item_payable_amt;
                        $Order->total_ordercost = $Order->total_ordercost - $order_item->total_item_amount;
                        $Order->payble_ordercost = $Order->payble_ordercost - $order_item->item_payable_amt;
                        $Order->save();
                    }
                    elseif ($request->order_status == 8 && $Order->payment_type != 1){
                        $Order->total_ordercost = $Order->total_ordercost - $order_item->total_item_amount;
                        $Order->save();
                    }
                    $order_item->save();
                }
            }
        }
        elseif ($old_order_status == 2 && ($request->order_status==3 || $request->order_status==8)){
            $Order->order_status = $request->order_status;
            if($request->order_status == 3 && $Order->delivery_date == null) {
                $Order->delivery_date = Carbon::now();
            }

            foreach ($Order->order_item as $order_item){
                if ($order_item->order_status != 6 && $order_item->order_status != 7 && $order_item->order_status != 8) {
                    $order_item->order_status = $request->order_status;
                    //For refund amount in case of Cancelled item
                    if ($request->order_status == 8 && $Order->payment_type == 1){
                        $Order->total_refund_amount = $Order->total_refund_amount + $order_item->item_payable_amt;
                        $Order->total_ordercost = $Order->total_ordercost - $order_item->total_item_amount;
                        $Order->payble_ordercost = $Order->payble_ordercost - $order_item->item_payable_amt;
                        $Order->save();
                    }
                    elseif ($request->order_status == 8 && $Order->payment_type != 1){
                        $Order->total_ordercost = $Order->total_ordercost - $order_item->total_item_amount;
                        $Order->save();
                    }
                    $order_item->save();
                }
            }
        }
        elseif ($old_order_status == 3 && $request->order_status==4){
            $Order->order_status = $request->order_status;
            foreach ($Order->order_item as $order_item){
                if ($order_item->order_status != 6 && $order_item->order_status != 7 && $order_item->order_status != 8) {
                    $order_item->order_status = $request->order_status;
                    $order_item->save();
                }
            }
        }
        elseif ($old_order_status == 4 && ($request->order_status==6 || $request->order_status==3)){
            $Order->order_status = $request->order_status;
            if($request->order_status == 3 && $Order->delivery_date == null) {
                $Order->delivery_date = Carbon::now();
            }
            $Order->save();

            foreach ($Order->order_item as $order_item){
                if ($order_item->order_status != 6 && $order_item->order_status != 7 && $order_item->order_status != 8) {
                    $order_item->order_status = $request->order_status;
                    //For refund amount in case of Returned item
                    if ($request->order_status == 6){
                        $Order->total_refund_amount = $Order->total_refund_amount + $order_item->item_payable_amt;
                        $Order->payble_ordercost = $Order->payble_ordercost - $order_item->item_payable_amt;
                        $Order->save();
                    }
                    //For tillreturned_date
                    /*if($request->order_status == 3 && $order_item->tillreturned_date == null) {
                        $item_details = json_decode($order_item->item_details,true);
                        $cat_id = Product::whereHas('product_variant',function ($mainQuery) use($item_details) {
                            $mainQuery->where('id',$item_details['variantId']);
                        })->pluck('primary_category_id')->first();
                        $order_return_days = Category::find($cat_id);
                        $order_item->tillreturned_date = Carbon::parse($Order->delivery_date)->addDays($order_return_days->order_return_days);
                    }*/
                    $order_item->save();
                }
            }
        }
        elseif ($old_order_status == 5 && $request->order_status==6){
            $Order->order_status = $request->order_status;
            foreach ($Order->order_item as $order_item){
                if ($order_item->order_status != 6 && $order_item->order_status != 7 && $order_item->order_status != 8) {
                    $order_item->order_status = $request->order_status;
                    $order_item->save();
                }
            }
        }

        $Order->save();

        //For count Monthly total commission amount user wise (delivered status)
        if ($old_order_status==2 && $Order->order_status==3){
            $current_year = date("Y");
            $current_month = date("m");
            $commissions = Commission::where('order_id',$Order->id)->get();
            foreach ($commissions as $commission){
                $monthly_commission = MonthlyCommission::where('user_id',$commission->user_id)->where('current_month',$current_month)->where('current_year',$current_year)->first();
                if (!$monthly_commission){
                    $monthly_commission = new MonthlyCommission();
                    $monthly_commission->user_id = $commission->user_id;
                    $monthly_commission->total_amount = $commission->amount;
                    $monthly_commission->current_month = $current_month;
                    $monthly_commission->current_year = $current_year;
                    $monthly_commission->save();
                }
                else{
                    $monthly_commission->total_amount = $monthly_commission->total_amount + $commission->amount;
                    $monthly_commission->save();
                }

                $commission->monthly_commission_id = $monthly_commission->id;
                $commission->save();
            }
        }

        //For minus commission amount in case of product Returned (approve return request)
        if ($old_order_status==4 && $Order->order_status==6){
            minus_commission_amount($Order->id);
        }

        //For cancel commission amount in case of cancel order
        if ( ($old_order_status==1 || $old_order_status==2) && $Order->order_status==8){
            $commissions = Commission::where('order_id',$Order->id)->get();
            foreach ($commissions as $commission){
                $commission->commission_status = 4;
                $commission->save();
            }
        }

        if($old_order_status==1 && $Order->order_status==2){
            $notification_array['title'] = "Out for Delivery";
            $notification_array['message'] = "Your order ". $Order->custom_orderid ." will arrive today.";
            sendPushNotification_updateOrder($Order->user_id,$notification_array);
            $this->save_notification($Order->user_id,$notification_array);
        }
        elseif($old_order_status==2 && $Order->order_status==3){
            $notification_array['title'] = "Order Delivered";
            $notification_array['message'] = "Your order ". $Order->custom_orderid ." is Delivered.";
            sendPushNotification_updateOrder($Order->user_id,$notification_array);
            $this->save_notification($Order->user_id,$notification_array);
        }
        elseif($old_order_status==4 && $Order->order_status==6){
            $notification_array['title'] = "Request Approved";
            $notification_array['message'] = "Your Return Request has been Approved for ".$Order->custom_orderid;
            sendPushNotification_updateOrder($Order->user_id,$notification_array);
            $this->save_notification($Order->user_id,$notification_array);
        }
        elseif($old_order_status==4 && $Order->order_status==3){
            $notification_array['title'] = "Request Rejected";
            $notification_array['message'] = "Your Return Request has been Rejected for ".$Order->custom_orderid;
            sendPushNotification_updateOrder($Order->user_id,$notification_array);
            $this->save_notification($Order->user_id,$notification_array);
        }

        return ['status' => 200];
    }

    public function save_notification($user_id,$notification_array){
        $Notification = new Notification();
        $Notification->user_id = $user_id;
        $Notification->notify_title = $notification_array['title'];
        $Notification->notify_desc = $notification_array['message'];
        $Notification->type = "order";
        $Notification->save();
    }

    public function change_order_status(Request $request){
        if (isset($request->order_id)) {
            $order = Order::find($request->order_id);
            $old_order_status = $order->order_status;
            if (!$order) {
                return ['status' => 400];
            }

            if (isset($request->action) && $request->action == 'approve'){
                $order->order_status = 6;
                $order->save();
                if ($old_order_status==4 && $order->order_status==6){
                    minus_commission_amount($order->id);
                }
                return ['status' => 200];
            }

            if (isset($request->action) && $request->action == 'reject'){
                $order->order_status = 3;
                $order->save();
                return ['status' => 200];
            }
        }

        if (isset($request->item_id)){
            $orderItem = OrderItem::find($request->item_id);
            if (!$orderItem) {
                return ['status' => 400];
            }

            if (isset($request->action) && $request->action == 'item_approve'){
                $old_item_status = $orderItem->order_status;
                $orderItem->order_status = 6;
                $orderItem->save();

                //For refund amount in case of Returned item
                if ($old_item_status == 4 && $orderItem->order_status == 6){
                    $orderItem->order->total_refund_amount = $orderItem->order->total_refund_amount + $orderItem->total_item_amount;
                    $orderItem->order->total_ordercost = $orderItem->order->total_ordercost - $orderItem->total_item_amount;
                    $orderItem->order->save();
                }
                return ['status' => 200];
            }

            if (isset($request->action) && $request->action == 'item_reject'){
                $orderItem->order_status = 3;
                $orderItem->save();
                return ['status' => 200];
            }
        }


        return ['status' => 400];
    }

    public function change_order_item_status(Request $request){
        $order_item = OrderItem::with('order')->where('id',$request->item_id)->first();
        if (!$order_item){
            return ['status' => 400];
        }

        if ($request->item_action == "return_request"){
            $order_item->order_status = 4;
        }
        elseif ($request->item_action == "cancel"){
            $order_item->order_status = 8;
            //For refund amount in case of Cancelled item
            if ($order_item->order->payment_type == 1){
                $order_item->order->total_refund_amount = $order_item->order->total_refund_amount + $order_item->item_payable_amt;
                $order_item->order->total_ordercost = $order_item->order->total_ordercost - $order_item->total_item_amount;
                $order_item->order->payble_ordercost = $order_item->order->payble_ordercost - $order_item->item_payable_amt;
                $order_item->order->save();
            }
            elseif ($order_item->order->payment_type != 1){
                $order_item->order->total_ordercost = $order_item->order->total_ordercost - $order_item->total_item_amount;
                $order_item->order->save();
            }
        }
        elseif ($request->item_action == "reject"){
            $order_item->order_status = 3;
        }
        elseif ($request->item_action == "approve"){
            $order_item->order_status = 6;
            //For refund amount in case of Returned item
            $order_item->order->total_refund_amount = $order_item->order->total_refund_amount + $order_item->item_payable_amt;
            $order_item->order->payble_ordercost = $order_item->order->payble_ordercost - $order_item->item_payable_amt;
            $order_item->order->save();
        }

        $order_item->save();
        return ['status' => 200];
    }

    public function return_requests(){
        return view('admin.return_requests.list')->with('page',$this->page);
    }

    public function allReturnRequestlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'image',
                2 => 'order_id',
                3 => 'product_info',
                4 => 'price_info',
                5 => 'payment_status',
                6 => 'created_at',
                7 => 'action',
            );


            $totalData = OrderItem::whereIn('order_status',[4,6])->count();

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
                $return_requests = OrderItem::with('order')
                    ->whereIn('order_status',[4,6])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $return_requests = OrderItem::query();
                $return_requests =  $return_requests->with('order')
                    ->whereIn('order_status',[4,6])
                    ->where(function($query) use($search,$return_requests){
                        $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('created_at', 'LIKE',"%{$search}%");
//                            ->orWhere('product_title', 'LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($return_requests->toArray());
            }

            $data = array();

            if(!empty($return_requests))
            {
                foreach ($return_requests as $return_request)
                {
                    $page_id = ProjectPage::where('route_url','admin.return_requests.list')->pluck('id')->first();

                    $product_info = '';
                    if (isset($return_request->product_title)){
                        $product_info .= '<b>'.$return_request->product_title.'</b>';
                    }
                    if (isset($return_request->attributename)){
                        $product_info .= '<span>Attribute: '.$return_request->attributename.'</span>';
                    }
                    if (isset($return_request->attributetermname)){
                        $product_info .= '<span>Attribute Term: '.$return_request->attributetermname.'</span>';
                    }

                    $price_info = '';
                    $qty = '';
                    if (isset($return_request->item_quantity)){
                        $qty = ' &times; '.$return_request->item_quantity.' Qty';
                    }
                    if (isset($return_request->order_item_price)){
                        $price_info .= '<span>Item Price: <i class="fa fa-inr" aria-hidden="true"></i> '.$return_request->order_item_price.$qty.'</span>';
                    }
                    if (isset($return_request->sub_discount)){
                        $price_info .= '<span>Sub Discount: <i class="fa fa-inr" aria-hidden="true"></i> '.$return_request->sub_discount.'</span>';
                    }
                    if (isset($return_request->total_item_amount)){
                        $price_info .= '<span>total Amount: <i class="fa fa-inr" aria-hidden="true"></i> '.$return_request->total_item_amount.'</span>';
                    }
                    if (isset($return_request->item_payable_amt)){
                        $price_info .= '<span>Payable Amount: <i class="fa fa-inr" aria-hidden="true"></i> '.$return_request->item_payable_amt.'</span>';
                    }

                    if(isset($return_request->payment_status)) {
                        $payment_status = getPaymentStatus($return_request->payment_status);
                        $payment_status = '<span class="'.$payment_status['class'].'">'.$payment_status['payment_status'].'</span>';
                    }

                    $action = '';
                    if( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ) {
                        $action .= '<button id="ViewOrderBtn" class="btn gradient-9 btn-sm" onclick="editOrder(' . $return_request->order_id . ')"><i class="fa fa-eye" aria-hidden="true"></i></button>';
                    }
                    if( isset($return_request->order_status) && $return_request->order_status == 4 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $action .= '<button type="button" class="btn mb-1 btn-success btn-xs" data-id="'.$return_request->id.'" id="ApproveReturnRequestBtn">Approve</button>';
                        $action .= '<button type="button" class="btn mb-1 btn-danger btn-xs" data-id="'.$return_request->id.'" id="RejectReturnRequestBtn">Reject</button>';
                    }

                    $returnreq_images = explode(",",$return_request->order_return_imgs);
                    $returnreq_images_paths = array_map(function ($val){
                        return url('public/'.$val);
                    }, $returnreq_images);
                    $returnreq_images_paths = "['".implode("','",$returnreq_images_paths)."']";

                    $action .= '<span class="returnReqImgs" id="returnReqImgs_'.$return_request->id.'">
                                <a href="javascript:void(0)" class="btn btn-dark btn-sm"><i class="fa fa-image"></i></a>
                                <script type="text/javascript">
                                    $("#returnReqImgs_'.$return_request->id.'").slickLightbox({images: '.$returnreq_images_paths.'});
                                </script>
                                </span>';
                    $action .= '<button id="VideoBtn" class="btn btn-sm text-blue" data-id="'.$return_request->id.'" data-toggle="modal" data-target="#ReturnReqVideoModal"><i class="fa fa-video-camera" aria-hidden="true"></i></button>';

                    $nestedData['image'] = '<img src="'.url($return_request->item_image).'" width="50px" height="50px" alt="Profile Pic">';
                    $nestedData['order_id'] = $return_request->order->custom_orderid;
                    $nestedData['product_info'] = $product_info;
                    $nestedData['price_info'] = $price_info;
                    $nestedData['payment_status'] = $payment_status;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($return_request->created_at));
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

    public function generate_pdf($id){
        try{
            $Order = Order::with('order_item','user')->where('id',$id)->first();
            $delivery_address = json_decode($Order->delivery_address,true);

            $HTMLContent = '<style type="text/css">
                            <!--
                            table { vertical-align: top; }
                            tr    { vertical-align: top; }
                            td    { vertical-align: top; }
                            -->
                            </style>
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
            $HTMLContent .= '<page backcolor="#FEFEFE" style="font-size: 12pt">
                        <bookmark title="Lettre" level="0" ></bookmark>
                        <table cellspacing="0" style="width: 100%; text-align: center; font-size: 14px; border-bottom: dotted 1px black;">
                            <tr>
                                <td style="width: 25%; color: #444444;">
                                    <img style="width: 100%;" src="'.url('public/images/madness_logo.png').'" alt="Logo"><br>
                                </td>
                                <td style="width: 50%;">
                                	<h3 style="text-align: center; font-size: 20pt; margin-bottom: 0;">Invoice</h3>
                                </td>
                                <td style="width: 25%;">
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table cellspacing="0" style="width: 100%;">
                            <colgroup>
                                <col style="width: 62%;">
                                <col style="width: 38%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td style="font-size: 12pt; padding:2px 0;">
                                        Buyer
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;text-align: right">
                                        Order ID: '.$Order->custom_orderid.'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;">
                                        Name: '.$delivery_address['CustomerName'].'
                                    </td>
                                    <td style="font-size: 10pt; padding:2px 0;text-align: right">
                                        Order Date: '.date('d-m-Y', strtotime($Order->created_at)).'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;" colspan="2">
                                        Mobile No: '.$delivery_address['CustomerMobile'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;" colspan="2">
                                        Address: '.$delivery_address['DelAddress1'].','.$delivery_address['Landmark'].',
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10pt; padding:2px 0;" colspan="2">
                                        '.$delivery_address['City'].','.$delivery_address['State'].','.$delivery_address['Country'].','.$delivery_address['Pincode'].'.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table cellspacing="0" style="width: 100%; margin-top:10px;  font-size: 10pt; margin-bottom:10px;" align="center">
                            <colgroup>
                                <col style="width: 10%; text-align: center">
                                <col style="width: 40%; text-align: left">
                                <col style="width: 16%; text-align: center">
                                <col style="width: 15%; text-align: center">
                                <col style="width: 19%; text-align: center">
                            </colgroup>
                            <thead>
                                <tr style="background: #ffe6e6;   ">
                                    <th colspan="5" style="text-align: center; border-top : solid 1px gray; border-bottom: solid 1px grey;  padding:8px 0;"> Item Details </th>
                                </tr>
                                <tr>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">No.</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Item Description</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Price</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Qty</th>
                                    <th style="border-bottom: solid 1px gray; padding:8px 0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>';

            $no = 1;
            foreach ($Order->order_item as $order_item){
                $item_details = json_decode($order_item->item_details,true);

                $item = '';
                if (isset($item_details['ProductTitle'])){
                    $item .= '<span>'.$item_details['ProductTitle'].'</span><br>';
                }
                if (isset($item_details['attribute']) && isset($item_details['attributeTerm'])){
                    $item .= '<span>'.$item_details['attribute'].': '.$item_details['attributeTerm'].'</span>';
                }

                $HTMLContent .= '<tr>
                                    <th style="font-weight : 10px; padding:8px 0;">'.$no.'</th>
                                    <th style="font-weight : 10px; padding:8px 0;">'.$item.'</th>
                                    <th style="font-weight : 10px; padding:8px 0;">'.number_format($item_details['orderItemPrice'], 2, '.', ',').'</th>
                                    <th style="font-weight : 10px; padding:8px 0;">'.$item_details['itemQuantity'].'</th>
                                    <th style="font-weight : 10px; padding:8px 0;">'.number_format($item_details['totalItemAmount'], 2, '.', ',').'</th>
                                </tr>';
                $no++;
            }

            $HTMLContent .= '<tr>
                                    <td colspan="5" style="padding:4px 0;"></td>
                             </tr>
                             <tr>
                                    <th colspan="4" style="padding:10px 0; border-top : solid 0.5px black;">Subtotal</th>
                                    <td  style="padding:10px 0; border-top : solid 0.5px black;">'.number_format($Order->sub_totalcost, 2, '.', ',').'</td>
                             </tr>
                             <tr>
                                    <th colspan="4" style="padding:10px 0; border-top : solid 0.5px black;">Shipping Cost</th>
                                    <td  style="padding:10px 0; border-top : solid 0.5px black;">'.number_format($Order->shipping_charge, 2, '.', ',').'</td>
                             </tr>
                             <tr>
                                    <th colspan="4" style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">Coupan Discount</th>
                                    <td  style="padding:10px 0; border-top : solid 0.5px black; border-bottom: solid 1px black;">'.number_format($Order->discount_amount, 2, '.', ',').'</td>
                             </tr>
                             <tr>
                                    <td colspan="2" style="padding:8px 0; border-bottom: solid 1px black;"></td>
                                    <th colspan="2" style="padding:8px 0; text-align:left; padding-left : 10px; border-bottom: solid 1px black; border-left: solid 1px black;">Grand Total</th>
                                    <td style="padding:8px 0; border-bottom: solid 1px black;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>'.number_format($Order->total_ordercost, 2, '.', ',').'</td>
                             </tr>
                            </tbody>
                        </table>
                        </page>';

            $html2pdf = new Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($HTMLContent);
            $html2pdf->pdf->IncludeJS('<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>');
            $html2pdf->output($Order->custom_orderid.'.pdf');
        } catch (Html2PdfException $e) {
            $html2pdf->clean();

            $formatter = new ExceptionFormatter($e);
            echo $formatter->getHtmlMessage();
        }
    }

    public function orderitem_play_video($order_item_id){
        $orderItem = OrderItem::find($order_item_id);
        $infoPath = pathinfo(public_path($orderItem->order_return_video));
        $extension = $infoPath['extension'];

        return ['order_return_video' => url('public/'.$orderItem->order_return_video), 'type' => 'video/'.$extension];
    }

    public function order_play_video($order_id){
        $Order = Order::find($order_id);
        $infoPath = pathinfo(public_path($Order->order_return_video));
        $extension = $infoPath['extension'];

        return ['order_return_video' => url('public/'.$Order->order_return_video), 'type' => 'video/'.$extension];
    }
}
