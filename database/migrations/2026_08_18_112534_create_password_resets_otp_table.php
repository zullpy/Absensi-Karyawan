<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets_otp', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp', 20)->index();
            $table->string('otp', 6);
            $table->timestamp('expired_at');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets_otp');
    }
};
