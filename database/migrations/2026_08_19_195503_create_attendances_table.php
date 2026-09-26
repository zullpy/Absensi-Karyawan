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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->string('photo_in');
            $table->string('photo_out')->nullable();
            $table->decimal('lat_in', 10, 7)->nullable();
            $table->decimal('long_in', 10, 7)->nullable();
            $table->decimal('lat_out', 10, 7)->nullable();
            $table->decimal('long_out', 10, 7)->nullable();
            $table->enum('status', ['present', 'late'])->default('present');
            $table->decimal('distance_in_meters', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
