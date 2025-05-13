<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         schema::table('orders',function(Blueprint $table){
            $table->enum('payment_status',['paid','not paid'])->after('grand_total')->default('not paid');
        });
         schema::table('orders',function(Blueprint $table){
            $table->enum('status',['pending','shipped','delivered'])->after('payment_status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         schema::table('orders',function(Blueprint $table){
            $table->dropColumn('payment_status');
            $table->dropColumn('status');
        });
    }
};
