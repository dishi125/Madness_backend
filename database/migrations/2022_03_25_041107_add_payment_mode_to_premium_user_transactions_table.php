<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentModeToPremiumUserTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('premium_user_transactions', function (Blueprint $table) {
            $table->text('payment_mode')->after('transaction_id');
            $table->dateTime('transaction_date')->nullable()->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('premium_user_transactions', function (Blueprint $table) {
            //
        });
    }
}
