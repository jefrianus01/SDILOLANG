<?php

namespace App\Models;

use App\Support\Roles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo_path',
        'must_change_password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Kelas yang diampu sebagai wali kelas pada tahun ajaran aktif.
     */
    public function homeroomClassrooms(): Collection
    {
        if (! $this->teacher) {
            return collect();
        }

        return Classroom::query()
            ->where('wali_kelas_id', $this->teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('level')
            ->get();
    }

    public function homeroomClassroom(): ?Classroom
    {
        return $this->homeroomClassrooms()->first();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Roles::SUPER_ADMIN);
    }

    public function isKepalaSekolah(): bool
    {
        return $this->hasRole(Roles::KEPALA_SEKOLAH);
    }

    public function isWaliKelas(): bool
    {
        return $this->hasRole(Roles::WALI_KELAS);
    }

    public function isGuru(): bool
    {
        return $this->hasRole(Roles::GURU);
    }

    /**
     * Super admin dan kepala sekolah boleh melihat seluruh data (read-only untuk siswa).
     */
    public function canSeeAllStudents(): bool
    {
        return $this->isSuperAdmin() || $this->isKepalaSekolah();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
