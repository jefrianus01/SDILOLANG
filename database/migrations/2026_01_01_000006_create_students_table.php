<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk_siswa', 30)->unique();
            $table->string('nama', 150);
            $table->foreignId('classroom_id')->constrained('classrooms')->restrictOnDelete();
            $table->string('status', 20)->default('aktif'); // aktif / nonaktif / pindah / lulus
            $table->softDeletes();
            $table->timestamps();

            $table->index(['classroom_id', 'status', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
