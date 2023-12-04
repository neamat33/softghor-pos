<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pos_id');
            $table->string('product_name');
            $table->unsignedInteger('product_id');
            $table->decimal('rate',10,2);

            $table->integer('main_unit_qty')->nullable();
            $table->integer('sub_unit_qty')->nullable();
            $table->integer('qty');

            $table->decimal('sub_total',12,2);
            $table->decimal('total_purchase_cost', 12, 2);
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
        Schema::dropIfExists('pos_items');
    }
}
