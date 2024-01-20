<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdBanner;
use App\Models\AdGroup;
use App\Models\Category;
use App\Models\Collection;
use App\Models\HomepageBanner;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function home(Request $request){
        $variants = VariantsList("",env('LIMIT'),"",$request->user_id,"","",false);

        $parent_categories = Category::where('parent_category_id',0)->where('estatus',1)->orderBy('sr_no','ASC')->get();
        $parent_categories_arr = array();
        foreach ($parent_categories as $parent_category){
            $temp = array();
            $temp['id'] = $parent_category->id;
            $temp['category_name'] = $parent_category->category_name;
            $temp['category_thumb'] = 'public/'.$parent_category->category_thumb;
            $temp['total_products'] = $parent_category->total_products;
            array_push($parent_categories_arr,$temp);
        }

        $homepage_banners = HomepageBanner::with('applicationdropdown')->where('estatus',1)->orderBy('sr_no','ASC')->get();
        $homepage_banners_arr = array();
        foreach ($homepage_banners as $homepage_banner){
            $temp = array();
            $temp['id'] = $homepage_banner->id;
            $temp['image'] = 'public/'.$homepage_banner->image;
            $temp['application_dropdown'] = $homepage_banner->applicationdropdown->title;

            if($homepage_banner->application_dropdown_id == 5){
                $category = Category::where('id',$homepage_banner->value)->first();
                $product = ProductVariant::where('id',$homepage_banner->product_variant_id)->pluck('product_title')->first();
                $temp['value_id'] = $homepage_banner->product_variant_id;
                $temp['value_title'] = $product;
            }
            elseif($homepage_banner->application_dropdown_id == 7){
                $category = Category::where('id',$homepage_banner->value)->first();
                $temp['value_id'] = $category->id;
                $temp['value_title'] = $category->category_name;
            }
            else{
                $temp['value_id'] = null;
                $temp['value_title'] = $homepage_banner->value;
            }

            array_push($homepage_banners_arr,$temp);
        }

        $adGroups = AdGroup::with('adview','adbanner.applicationdropdown')->where('category_id',0)->where('estatus',1)->get();
        $adGroups_arr = array();
        foreach ($adGroups as $adGroup){
            array_push($adGroups_arr,adgroup_detail($adGroup));
        }

        $data['parent_categories'] = $parent_categories_arr;
        $data['sliders'] = $homepage_banners_arr;
        $data['variants'] = $variants['variants'];
        $data['ad_groups'] = $adGroups_arr;
        return $this->sendResponseWithData($data,"Product Variants Retrieved Successfully.");
    }

    public function product_details(Request $request){
        $validator = Validator::make($request->all(), [
            'variant_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $variant = ProductVariant::where('id',$request->variant_id)->where('estatus',1)->first();

        if(empty($variant)){
            return $this->sendError("Product Not Exist.", "Not Found Error", []);
        }

        $product = Product::where('id',$variant->product_id)->first();
        if(isset($product->subchild_category_id)){
            $cat_id = $product->subchild_category_id;
        }
        else{
            $cat_id = $product->child_category_id;
        }

        $products1 = Product::where('subchild_category_id',$cat_id)->where('estatus',1)->pluck('id')->toArray();
        $products2 = Product::where('child_category_id',$cat_id)->where('subchild_category_id',null)->where('estatus',1)->pluck('id')->toArray();
        $product_ids = array_merge($products1,$products2);
        $related_products_arr = array();
        $product_variants = ProductVariant::with('attribute_term','product_variant_specification.attribute','product_variant_specification.attribute_term')->whereIn('product_id',$product_ids)->where('id',"!=",$request->variant_id)->where('estatus',1)->limit(env('LIMIT'))->orderBy('created_at','desc')->get();
        foreach ($product_variants as $product_variant){
            array_push($related_products_arr,product_variant_detail($product_variant,$request->user_id));
        }

        $variant_detail = VariantsList($request->variant_id,"","",$request->user_id,"","",false);
        $all_variants = VariantsList("",env('LIMIT'),"",$request->user_id,"","",false);

        $data['variant_details'] = $variant_detail['variants'];
        $data['related_products'] = $related_products_arr;
        $data['all_variants'] = $all_variants['variants'];
        return $this->sendResponseWithData($data,"Product Details Retrieved Successfully.");
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'search' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $categories = Category::where('category_name', 'LIKE',"%{$request->search}%")->where('estatus',1)->orderBy('sr_no','ASC')->get();
        $categories_arr = array();
        foreach ($categories as $category){
            array_push($categories_arr,category_detail($category));
        }

        $product_variants = ProductVariant::with('product','attribute_term','product_variant_specification.attribute','product_variant_specification.attribute_term')
            ->where('product_title','LIKE',"%{$request->search}%")
            ->where('estatus',1)
            ->orderBy('created_at','desc')
            ->paginate(env('PER_PAGE'));

        $variants_arr = array();
        foreach ($product_variants as $product_variant){
            array_push($variants_arr,product_variant_detail($product_variant,$request->user_id));
        }

        $data['categories'] = $categories_arr;
        $data['products'] = $variants_arr;
        $data['total_records'] = $product_variants->toArray()['total'];

        if ($request->user_id!=0 && $request->user_id!=null) {
            $searchHistory = new SearchHistory();
            $searchHistory->user_id = $request->user_id;
            $searchHistory->search_string = $request->search;
            $searchHistory->save();
        }
        return $this->sendResponseWithData($data,"Search Data Retrieved Successfully.");
    }

    public function view_products(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'action' => 'required',
            'action_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        if ($request->action == "slider"){
            $data = HomepageBanner::where('id',$request->action_id)->where('estatus',1)->first();
        }
        elseif ($request->action == "ad_banner"){
            $data = AdBanner::where('id',$request->action_id)->where('estatus',1)->first();
        }
        elseif ($request->action == "collection"){
            $data = Collection::where('id',$request->action_id)->where('estatus',1)->first();
        }

        if ($data->application_dropdown_id == 3){
            $variants = VariantsList("","",env('PER_PAGE'),$request->user_id,$data->value,"",false);
        }
        elseif ($data->application_dropdown_id == 10){
            $variants = VariantsList("","",env('PER_PAGE'),$request->user_id,"",$data->value,false);
        }
        elseif ($data->application_dropdown_id == 6){
            $variants = VariantsList("","",env('PER_PAGE'),$request->user_id,"",$data->value,true);
        }

        $res_data = array();
        if (isset($variants)) {
            array_push($res_data,$variants);
        }
        return $this->sendResponseWithData($res_data,"Products Retrieved Successfully.");
    }

    public function filter_products(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $category_ids = explode(",",$request->category_id);

        $product_variants = ProductVariant::with('product','attribute_term','product_variant_specification.attribute','product_variant_specification.attribute_term')
            ->where('estatus',1)
            ->whereHas('product',function ($query) use($request, $category_ids) {
                $query->whereIn('primary_category_id',$category_ids);
                $query->orWhereIn('child_category_id',$category_ids);
                $query->orWhereIn('subchild_category_id',$category_ids);
            });

        if (isset($request->min_price) && isset($request->max_price)){
            $product_variants = $product_variants->whereBetween('sale_price',[$request->min_price,$request->max_price]);
        }

        if (isset($request->sort_order) && $request->sort_order=="asc"){
            $product_variants = $product_variants->orderBy('sale_price','ASC');
        }

        if (isset($request->sort_order) && $request->sort_order=="desc"){
            $product_variants = $product_variants->orderBy('sale_price','DESC');
        }

        if (isset($request->item_popular) && $request->item_popular==1){
            $product_variants = $product_variants->orderBy('total_orders','DESC');
        }

        $product_variants = $product_variants->get();

        $variants_arr = array();
        foreach ($product_variants as $product_variant){
            array_push($variants_arr,product_variant_detail($product_variant,$request->user_id));
        }

        $data['all_products'] = $variants_arr;
        return $this->sendResponseWithData($data,"Products Retrieved Successfully.");
    }
}
