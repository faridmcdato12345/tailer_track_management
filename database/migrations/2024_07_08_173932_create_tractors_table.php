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
        Schema::create('tractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('tractor_number');
            $table->float('maximum_capacity')->nullable();
            $table->float('load_status')->nullable()->default(0);
            $table->enum('status',['Available In Plant','In Use','Ready To Pick Up','Picked Up','Complete','Dropped Off','Ready To Pick Up From Yard','Picked Up From Yard']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
