<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('other')->hasTable('other_phones')) {
            return;
        }

        Schema::connection('other')->create('other_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('phone_numbers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('other_phones');
    }
};
