<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('nip_nik', 18)->unique(); // hanya angka, 16-18 digit
            $table->foreignId('teacher_type_id')->constrained('teacher_types')->restrictOnDelete();
            $table->string('status', 20)->default('aktif'); // aktif / nonaktif
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
