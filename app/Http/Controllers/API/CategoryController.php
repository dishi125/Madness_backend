<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdGroup;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public function view_category(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $categories = Category::where('parent_category_id',$request->category_id)->where('estatus',1)->orderBy('sr_no','ASC')->get();

        $product_variants = ProductVariant::with('product','attribute_term','product_variant_specification.attribute','product_variant_specification.attribute_term')
                    ->where('estatus',1)
                    ->whereHas('product',function ($query) use($request) {
                        $query->where('primary_category_id',$request->category_id);
                        $query->orWhere('child_category_id',$request->category_id);
                        $query->orWhere('subchild_category_id',$request->category_id);
                    })
                    ->orderBy('created_at','desc')
                    ->paginate(env('PER_PAGE'));
//        dd($product_variants->toArray());

        $variants_arr = array();
        foreach ($product_variants as $product_variant){
            array_push($variants_arr,product_variant_detail($product_variant,$request->user_id));
        }

        $adGroups = AdGroup::with('adview','adbanner.applicationdropdown')->where('category_id',$request->category_id)->where('estatus',1)->get();
        $adGroups_arr = array();
        foreach ($adGroups as $adGroup){
            array_push($adGroups_arr,adgroup_detail($adGroup));
        }

        $categories_arr = array();
        foreach ($categories as $category){
            $temp = array();
            $temp['id'] = $category->id;
            $temp['category_name'] = $category->category_name;
            $temp['category_thumb'] = 'public/'.$category->category_thumb;
            $temp['total_products'] = $category->total_products;
            array_push($categories_arr,$temp);
        }

        $data['categories'] = $categories_arr;
        $data['all_products'] = $variants_arr;
//        $data['first_page_url'] = $product_variants->toArray()['first_page_url'];
//        $data['last_page_url'] = $product_variants->toArray()['last_page_url'];
//        $data['next_page_url'] = $product_variants->toArray()['next_page_url'];
//        $data['prev_page_url'] = $product_variants->toArray()['prev_page_url'];
        $data['total_records'] = $product_variants->toArray()['total'];
        $data['ad_groups'] = $adGroups_arr;
        return $this->sendResponseWithData($data,"Category Data Retrieved Successfully.");
    }

    public function all_categories(Request $request){
        $categories = Category::where('parent_category_id',0)->where('estatus',1)->orderBy('sr_no','ASC')->paginate(10);
        $categories_arr = array();
        foreach ($categories as $category){
            array_push($categories_arr,category_detail($category));
        }
        $data['categories'] = $categories_arr;
        $data['total_records'] = $categories->toArray()['total'];
        return $this->sendResponseWithData($data,"All Category Retrieved Successfully.");
    }

    public function filter_category(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $child_categories = Category::where('parent_category_id',$request->category_id)->where('estatus',1)->orderBy('sr_no','ASC')->get()->toArray();
        $child_categories_arr = array();
        foreach ($child_categories as $child_category){
            $temp['id'] = $child_category['id'];
            $temp['category_name'] = $child_category['category_name'];
            $temp['category_thumb'] = 'public/'.$child_category['category_thumb'];
            $temp['total_products'] = $child_category['total_products'];

            $sub_child_categories = Category::where('parent_category_id',$child_category['id'])->where('estatus',1)->orderBy('sr_no','ASC')->get();
            $temp['child_categories'] = array();
            foreach ($sub_child_categories as $sub_child_category){
                array_push($temp['child_categories'],category_detail($sub_child_category));
            }
            array_push($child_categories_arr,$temp);
        }

        $sale_price = ProductVariant::where('estatus',1)
            ->select(\DB::raw("MAX(sale_price) AS max_sale_price"), \DB::raw("MIN(sale_price) AS min_sale_price"))
            ->whereHas('product',function ($query) use($request) {
                $query->where('primary_category_id',$request->category_id);
                $query->orWhere('child_category_id',$request->category_id);
                $query->orWhere('subchild_category_id',$request->category_id);
            })
            ->get();

        $data['categories'] = $child_categories_arr;
        $data['sale_price'] = $sale_price;
        return $this->sendResponseWithData($data,"Category Retrieved Successfully.");
    }
}
