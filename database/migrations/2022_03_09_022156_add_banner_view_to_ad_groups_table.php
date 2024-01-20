<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerViewToAdGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ad_groups', function (Blueprint $table) {
            $table->integer('banner_view')->nullable()->comment("1->Horizontal, 2->Vertical")->after('ad_view_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ad_groups', function (Blueprint $table) {
            //
        });
    }
}
