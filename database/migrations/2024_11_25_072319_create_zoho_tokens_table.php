<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZohoTokensTable extends Migration
{
    public function up()
    {
        Schema::create('zoho_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('refresh_token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zoho_tokens');
    }
}
