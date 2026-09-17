<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('totens', function (Blueprint $table) {
            $table->string('nome')->nullable();
            $table->string('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('totens', function (Blueprint $table) {
            $table->dropColumn(['nome', 'status']);
        });
    }
};
