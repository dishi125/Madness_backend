<?php

namespace Database\Seeders;

use App\Models\ProjectPage;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_pages')->truncate();

        ProjectPage::create([
            'id' => 1,
            'parent_menu' => 0,
            'label' => 'Orders',
            'route_url' => null,
            'is_display_in_menu' => 1,
            'sr_no' => 1
        ]);

        ProjectPage::create([
            'id' => 2,
            'parent_menu' => 0,
            'label' => 'Products',
            'route_url' => null,
            'is_display_in_menu' => 1,
            'sr_no' => 2
        ]);

        ProjectPage::create([
            'id' => 3,
            'parent_menu' => 2,
            'label' => 'Category',
            'route_url' => 'admin.categories.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.categories.list,admin.categories.add,admin.categories.save,admin.allcategorylist,admin.categories.changecategorystatus,admin.categories.delete,admin.categories.edit,admin.categories.uploadfile,admin.categories.removefile,admin.categories.checkparentcat'
        ]);

        ProjectPage::create([
            'id' => 4,
            'parent_menu' => 2,
            'label' => 'Attributes & Specifications',
//            'route_url' => route('admin.attributes.list'),
            'route_url' => 'admin.attributes.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.attributes.list,admin.attributes.addorupdate,admin.allattributeslist,admin.attributes.edit,admin.attributes.delete,admin.attributes.chageattributestatus,admin.attributeTerms.list,admin.attributeTerms.addorupdate,admin.allattributesTermlist,admin.attributeTerms.chageattributeTermstatus,admin.attributeTerms.edit,admin.attributeTerms.delete'
        ]);

        ProjectPage::create([
            'id' => 5,
            'parent_menu' => 2,
            'label' => 'Product',
            'route_url' => 'admin.products.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.products.list,admin.products.add,admin.getAttrVariation,admin.addVariantbox,admin.products.save,admin.products.uploadfile,admin.products.removefile,admin.allproductlist,admin.products.edit,admin.products.changeproductstatus,admin.products.delete'
        ]);

        ProjectPage::create([
            'id' => 6,
            'parent_menu' => 0,
            'label' => 'Users',
            'route_url' => null,
            'is_display_in_menu' => 1,
            'sr_no' => 3
        ]);

        ProjectPage::create([
            'id' => 7,
            'parent_menu' => 6,
            'label' => 'User List',
            'route_url' => 'admin.users.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.users.list,admin.users.addorupdate,admin.alluserslist,admin.users.changeuserstatus,admin.users.edit,admin.users.delete,admin.users.permission,admin.users.savepermission'
        ]);

        ProjectPage::create([
            'id' => 8,
            'parent_menu' => 6,
            'label' => 'Customer List',
            'route_url' => 'admin.end_users.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.end_users.list,admin.end_users.addorupdate,admin.allEnduserlist,admin.end_users.changeEnduserstatus,admin.end_users.edit,admin.end_users.delete'
        ]);

        ProjectPage::create([
            'id' => 9,
            'parent_menu' => 0,
            'label' => 'Application',
            'route_url' => null,
            'is_display_in_menu' => 1,
            'sr_no' => 4
        ]);

        ProjectPage::create([
            'id' => 10,
            'parent_menu' => 9,
            'label' => 'Slider',
            'route_url' => 'admin.homepagebanners.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.homepagebanners.list,admin.homepagebanners.add,admin.homepagebanners.uploadfile,admin.homepagebanners.removefile,admin.homepagebanners.getBannerInfoVal,admin.homepagebanners.save,admin.allbannerlist,admin.homepagebanners.edit,admin.homepagebanners.delete,admin.homepagebanners.getproducts,admin.homepagebanners.changeBannerStatus'
        ]);

        ProjectPage::create([
            'id' => 11,
            'parent_menu' => 9,
            'label' => 'Ad Groups',
            'route_url' => 'admin.adgroups.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.adgroups.list,admin.adgroups.add,admin.adgroups.uploadfile,admin.adgroups.removefile,admin.adgroups.addBannerForm,admin.adgroups.save,admin.adgroups.getBannerInfoVal,admin.adgroups.getproducts,admin.alladgroupslist,admin.adgroups.changeAdGroupstatus,admin.adgroups.delete,admin.adgroups.edit'
        ]);

        ProjectPage::create([
            'id' => 12,
            'parent_menu' => 9,
            'label' => 'Levels',
            'route_url' => 'admin.levels.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.levels.list,admin.levels.addorupdate,admin.allLevelList,admin.levels.edit'
        ]);

        ProjectPage::create([
            'id' => 13,
            'parent_menu' => 0,
            'label' => 'Store',
            'route_url' => null,
            'is_display_in_menu' => 1,
            'sr_no' => 5
        ]);

        ProjectPage::create([
            'id' => 14,
            'parent_menu' => 13,
            'label' => 'Coupon',
            'route_url' => 'admin.coupons.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.coupons.list,admin.coupons.add,admin.coupons.save,admin.allcouponlist,admin.coupons.edit,admin.coupons.delete'
        ]);

        ProjectPage::create([
            'id' => 15,
            'parent_menu' => 0,
            'label' => 'Settings',
            'route_url' => 'admin.settings.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.settings.list,admin.settings.editUserDiscountPercentage,admin.settings.updateUserDiscountPercentage,admin.settings.editShippingCost,admin.settings.updateShippingCost,admin.settings.editPremiumUserMembershipFee,admin.settings.updatePremiumUserMembershipFee,admin.settings.editMinOrderAmount,admin.settings.updateMinOrderAmount',
            'sr_no' => 10
        ]);

        ProjectPage::create([
            'id' => 16,
            'parent_menu' => 1,
            'label' => 'Order',
            'route_url' => 'admin.orders.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.orders.list,admin.allOrderlist,admin.updateOrdernote,admin.orders.view,admin.orders.save,admin.change_order_status,admin.change_order_item_status,admin.orders.pdf,admin.orders.play_video'
        ]);

        ProjectPage::create([
            'id' => 17,
            'parent_menu' => 1,
            'label' => 'Return Request Items',
            'route_url' => 'admin.return_requests.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.return_requests.list,admin.allReturnRequestlist,admin.change_order_status,admin.return_requests.play_video'
        ]);

        ProjectPage::create([
            'id' => 18,
            'parent_menu' => 9,
            'label' => 'Collections',
            'route_url' => 'admin.collections.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.collections.list,admin.collections.add,admin.collections.uploadfile,admin.collections.removefile,admin.collections.getCollectionInfoVal,admin.collections.save,admin.allcollectionlist,admin.collections.edit,admin.collections.delete,admin.collections.getproducts,admin.collections.changeCollectionStatus'
        ]);

        ProjectPage::create([
            'id' => 19,
            'parent_menu' => 0,
            'label' => 'Monthly Commission',
            'route_url' => 'admin.monthly_commissions.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.monthly_commissions.list,admin.allMonthlyCommissionlist,admin.monthly_commissions.view',
            'sr_no' => 6
        ]);

        ProjectPage::create([
            'id' => 20,
            'parent_menu' => 9,
            'label' => 'Notifications',
            'route_url' => 'admin.notifications.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.notifications.list,admin.notifications.add,admin.notifications.getNotificationInfoVal,admin.notifications.getproducts,admin.notifications.uploadfile,admin.notifications.removefile,admin.notifications.save,admin.allnotificationlist,admin.notifications.edit,admin.notifications.delete'
        ]);

        ProjectPage::create([
            'id' => 21,
            'parent_menu' => 0,
            'label' => 'FAQ',
            'route_url' => 'admin.faq.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.faq.list,admin.faq.add,admin.faq.save,admin.allFaqlist,admin.faq.edit,admin.faq.delete',
            'sr_no' => 9
        ]);

        ProjectPage::create([
            'id' => 22,
            'parent_menu' => 0,
            'label' => 'Suggestions',
            'route_url' => 'admin.suggestions.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.suggestions.list,admin.allSuggestionslist',
            'sr_no' => 8
        ]);

        ProjectPage::create([
            'id' => 23,
            'parent_menu' => 0,
            'label' => 'Report',
            'is_display_in_menu' => 1,
            'sr_no' => 7
        ]);

        ProjectPage::create([
            'id' => 24,
            'parent_menu' => 23,
            'label' => 'Order Report',
            'route_url' => 'admin.order_report.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.order_report.list,admin.allOrderReportlist',
        ]);

        ProjectPage::create([
            'id' => 25,
            'parent_menu' => 23,
            'label' => 'Commission Report',
            'route_url' => 'admin.commission_report.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.commission_report.list,admin.allCommissionReportlist',
        ]);

        /*ProjectPage::create([
            'id' => 6,
            'parent_menu' => 0,
            'label' => 'Dashboard',
            'route_url' => 'admin.dashboard',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.dashboard'
        ]);*/

        $users = User::where('role',"!=",1)->get();
        $project_page_ids1 = ProjectPage::where('parent_menu',0)->where('is_display_in_menu',0)->pluck('id')->toArray();
        $project_page_ids2 = ProjectPage::where('parent_menu',"!=",0)->where('is_display_in_menu',1)->pluck('id')->toArray();
        $project_page_ids = array_merge($project_page_ids1,$project_page_ids2);
        foreach ($users as $user){
            foreach ($project_page_ids as $pid){
                $user_permission = UserPermission::where('user_id',$user->id)->where('project_page_id',$pid)->first();
                if (!$user_permission){
                    $userpermission = new UserPermission();
                    $userpermission->user_id = $user->id;
                    $userpermission->project_page_id = $pid;
                    $userpermission->save();
                }
            }
        }

    }
}
