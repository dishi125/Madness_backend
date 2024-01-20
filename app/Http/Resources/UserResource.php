<?php

namespace App\Http\Resources;

use App\Models\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'user_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_pic' => isset($this->profile_pic) ? 'public/images/profile_pic/'.$this->profile_pic : 'public/images/default_avatar.jpg',
            'mobile_no' => $this->mobile_no,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'email' => $this->email,
            'referral_id' => $this->referral_id,
            'is_premium' => $this->is_premium,
            'membership_amount' => Settings::find(1)->premium_user_membership_fee
        ];
    }
}
