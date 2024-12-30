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
        Schema::create('outs', function (Blueprint $table) {
            $table->id();
            $table->date('date_out');
            $table->integer('profit');
            $table->integer('amount_total_sale');
            $table->integer('amount_total_purchase');
            $table->string('ref', 30);
            $table->string('observation', 30)->nullable();
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outs');
    }
};
