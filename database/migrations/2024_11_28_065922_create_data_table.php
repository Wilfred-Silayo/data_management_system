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
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('cut');
            $table->string('color');
            $table->string('clarity');
            $table->decimal('carat_weight', 8, 2);
            $table->string('cut_quality');
            $table->string('lab');
            $table->string('symmetry');
            $table->string('polish');
            $table->string('eye_clean');
            $table->string('culet_size');
            $table->string('culet_condition');
            $table->decimal('depth_percent', 5, 2); 
            $table->decimal('table_percent', 5, 2); 
            $table->decimal('meas_length', 8, 2);
            $table->decimal('meas_width', 8, 2);
            $table->decimal('meas_depth', 8, 2);
            $table->string('girdle_min');
            $table->string('girdle_max');
            $table->string('fluor_color');
            $table->string('fluor_intensity');
            $table->string('fancy_color_dominant_color');
            $table->string('fancy_color_secondary_color');
            $table->string('fancy_color_overtone');
            $table->string('fancy_color_intensity');
            $table->decimal('total_sales_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
