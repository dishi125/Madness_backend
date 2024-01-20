<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends BaseController
{
    public function faq_list(){
        $Faqs = Faq::where('estatus',1)->orderBy('created_at','DESC')->get();
        $Faq_arr = array();
        foreach ($Faqs as $Faq){
            $temp = array();
            $temp['id'] = $Faq->id;
            $temp['question'] = $Faq->question;
            $temp['answer'] = $Faq->answer;

            array_push($Faq_arr,$temp);
        }

        return $this->sendResponseWithData($Faq_arr,"FAQ Retrieved Successfully.");
    }
}
