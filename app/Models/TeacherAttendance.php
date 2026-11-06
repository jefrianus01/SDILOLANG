<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\TeacherAttendanceStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance',
        'check_out_at',
        'check_out_lat',
        'check_out_lng',
        'check_out_distance',
        'status',
        'approval_status',
        'reason',
        'attachment_path',
        'approved_by',
        'approved_at',
        'approval_note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'approved_at' => 'datetime',
            'check_in_lat' => 'float',
            'check_in_lng' => 'float',
            'check_out_lat' => 'float',
            'check_out_lng' => 'float',
            'status' => TeacherAttendanceStatus::class,
            'approval_status' => ApprovalStatus::class,
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    /**
     * Simpan kolom date sebagai tanggal murni (Y-m-d) agar pencarian
     * berdasarkan tanggal tidak pernah membuat baris duplikat.
     */
    protected function date(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value === null || $value === ''
                ? null
                : \Carbon\Carbon::parse($value)->toDateString(),
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->approval_status === ApprovalStatus::Pending;
    }
}
