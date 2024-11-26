<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZohoAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoho_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('zoho_account_id')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('currency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoho_accounts');
    }
}
