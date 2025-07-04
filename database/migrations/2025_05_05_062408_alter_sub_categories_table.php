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
        schema::table('sub_categories',function(Blueprint $table){
            $table->enum('showHome',['Yes','No'])->after('status')->default('No');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('categories',function(Blueprint $table){
            $table->dropColumn('showHome');
        });
    }
};
