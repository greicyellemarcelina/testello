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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('from_postcode');
            $table->string('to_postcode');
            $table->decimal('from_weight');
            $table->decimal('to_weight');
            $table->decimal('cost');
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unique([
                'client_id',
                'from_postcode',
                'to_postcode',
                'from_weight',
                'to_weight'
            ], 'price_uk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
