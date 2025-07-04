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
            $table->timestamp('shipped_date')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         schema::table('orders',function(Blueprint $table){
            $table->dropColumn('shipped_date');
        });
    }
};
