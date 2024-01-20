<?php

namespace App\Http\Middleware\custom;

use App\Models\ProjectPage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
//        return $next($request);
        if( Auth::check() )
        {
//            dd($request->route()->getName());
            if (getUSerRole()==1){
                return $next($request);
            }
            elseif ($request->route()->getName() == 'admin.dashboard'){
                return $next($request);
            }
            else{
                $project_pages = ProjectPage::get();
                foreach ($project_pages as $project_page){
                    $inner_routes = explode(",",$project_page['inner_routes']);
                    if (isset($project_page['inner_routes']) && in_array($request->route()->getName(),$inner_routes)){
                        $page_id = $project_page['id'];
                        $user_permission = \App\Models\UserPermission::where('user_id',Auth::user()->id)
                            ->where('project_page_id',$page_id)
                            ->where(function($query) {
                                $query->where('can_read',1)
                                    ->orWhere('can_write', 1)
                                    ->orWhere('can_delete', 1);
                            })
                            ->first();
                        if ($user_permission){
                            if ($request->route()->getName()=='admin.attributes.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.attributes.chageattributestatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.attributes.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.attributeTerms.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.attributeTerms.chageattributeTermstatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.attributeTerms.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.users.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.users.changeuserstatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.users.permission' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.users.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.categories.changecategorystatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.categories.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.categories.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.categories.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.products.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.products.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.products.changeproductstatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.products.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.end_users.changeEnduserstatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.end_users.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.end_users.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.settings.editUserDiscountPercentage' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.settings.editShippingCost' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.settings.editPremiumUserMembershipFee' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.settings.editMinOrderAmount' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.homepagebanners.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.homepagebanners.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.homepagebanners.changeBannerStatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.homepagebanners.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.adgroups.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.adgroups.changeAdGroupstatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.adgroups.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.adgroups.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.levels.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.orders.view' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.coupons.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.coupons.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.coupons.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.collections.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.collections.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.collections.changeCollectionStatus' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.collections.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.notifications.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.notifications.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.notifications.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.faq.add' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.faq.edit' && $user_permission->can_write == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else if ($request->route()->getName()=='admin.faq.delete' && $user_permission->can_delete == 0){
                                return redirect(route('admin.403_page'));
                            }
                            else{
                                return $next($request);
                            }
                        }

                        else{
                            return redirect(route('admin.403_page'));
                        }

                    }
                }
            }
        }

        abort(404);
    }
}
