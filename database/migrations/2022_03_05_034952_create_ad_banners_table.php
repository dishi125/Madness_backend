<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_banners', function (Blueprint $table) {
            $table->id();
            $table->integer('ad_group_id');
            $table->text('ad_title');
            $table->text('image');
            $table->integer('application_dropdown_id');
            $table->text('value')->nullable()->comment('Based on Price -> price, Catelog -> catelog_id, Category -> category_id, New arrival -> arrival_days, URL -> banner_url');
            $table->integer('product_variant_id')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->dateTime('created_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(null)->onUpdate(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_banners');
    }
}
