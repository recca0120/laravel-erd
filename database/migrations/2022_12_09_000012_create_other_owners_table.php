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
        if (Schema::connection('other')->hasTable('other_owners')) {
            return;
        }

        Schema::connection('other')->create('other_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('car_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('other')->dropIfExists('other_owners');
    }
};
