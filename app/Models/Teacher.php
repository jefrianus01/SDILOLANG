<?php

namespace App\Models;

use App\Enums\TeacherStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nama',
        'nip_nik',
        'teacher_type_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => TeacherStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teacherType(): BelongsTo
    {
        return $this->belongsTo(TeacherType::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'wali_kelas_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function isAktif(): bool
    {
        return $this->status === TeacherStatus::Aktif;
    }
}
