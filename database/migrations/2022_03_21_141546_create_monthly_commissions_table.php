<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->float('total_amount');
            $table->integer('commission_status')->default(1)->comment('1->Pending, 2->Success, 3->On Hold, 4->Cancelled, 5->Failed');
            $table->integer('current_month');
            $table->integer('current_year');
            $table->dateTime('payment_date')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            $table->dateTime('updated_at')->default(null)->onUpdate(\Carbon\Carbon::now());
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
        Schema::dropIfExists('monthly_commissions');
    }
}
