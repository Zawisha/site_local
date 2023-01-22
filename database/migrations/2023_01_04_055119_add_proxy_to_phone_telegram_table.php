<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProxyToPhoneTelegramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_telegram', function (Blueprint $table) {
            $table->text('proxy_adres')->nullable();
            $table->text('proxy_port')->nullable();
            $table->text('proxy_username')->nullable();
            $table->text('proxy_password')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_telegram', function (Blueprint $table) {
            //
        });
    }
}
