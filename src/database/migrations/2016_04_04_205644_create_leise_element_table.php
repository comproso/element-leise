<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeiseElementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leise_elements', function (Blueprint $table) {
            $table->increments('id');
            #$table->integer('item_id')->unsigned();
            #$table->string('form_name');
            $table->string('label');
            $table->string('variable_name', 1);
            $table->enum('variable_type', ['manifest', 'latent']);
            $table->string('minimum')->nullable()->default(null);
            $table->string('maximum')->nullable()->default(null);
            $table->decimal('start_value', 10, 4)->nullable()->default(null);
            $table->string('formula')->nullable()->default(null);
            $table->string('unit', 50)->nullable()->default(null);
            $table->decimal('scale', 10, 4)->nullable()->default(null);
            $table->integer('decimal')->unsigned()->default(0);
            $table->string('icon')->nullable()->default(null);
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
        Schema::dropIfExists('leise_elements');
    }
}
