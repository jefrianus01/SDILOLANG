<?php

namespace App\Models;

use App\Enums\StudentStatus;
use App\Models\Scopes\StudentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_induk_siswa',
        'nama',
        'classroom_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new StudentScope);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Route binding tetap mencari siswa lintas kelas agar Policy dapat
     * mengembalikan 403 (bukan 404) saat wali kelas membuka siswa kelas lain.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return $query->withoutGlobalScope(StudentScope::class)
            ->where($field ?? $this->getRouteKeyName(), $value);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }
}
