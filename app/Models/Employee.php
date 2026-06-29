<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveRequest;
use App\Models\LeaveType;

class Employee extends Model
{
    protected $fillable = [


        'nik',
        'full_name',

        'email',
        'phone',

        'gender',
        'birth_place',
        'birth_date',

        'education',
        'photo',

        'ktp_number',
        'address',

        'company_id',
        'position_id',

        'user_id',
        'role_id',

        'join_date',

        'is_active',

        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function status()
    {
        return $this->belongsTo(
            EmployeeStatus::class,
            'employee_status_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(
            LeaveRequest::class
        );
    }
    public function getRemainingLeave($leaveTypeId)
    {
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return null;
        }

        // Jika tidak memiliki kuota (contoh: Izin Sakit)
        if (empty($leaveType->quota)) {
            return null;
        }

        $usedDays = $this->leaveRequests()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');


        return max(
            0,
            $leaveType->quota - $usedDays
        );
    }


    public function getLeaveSummary($leaveTypeId)
    {
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return null;
        }

        $used = $this->getUsedLeave($leaveTypeId);

        return [
            'quota' => $leaveType->quota,
            'used' => $used,
            'remaining' => max(0, $leaveType->quota - $used),
        ];
    }

    public function getUsedLeave($leaveTypeId)
    {
        return $this->leaveRequests()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear(
                'start_date',
                now()->year
            )
            ->sum('total_days');
    }

    public function teams()
    {
        return $this->hasMany(
            TeamMember::class
        );
    }

    public function teamMembers()
    {
        return $this->hasMany(
            TeamMember::class
        );
    }

    public function activeTeam()
    {
        return $this->hasOne(TeamMember::class)
            ->where('is_active', 1);
    }


    public function activeTeamMember()
    {
        return $this->hasOne(TeamMember::class)
            ->where('is_active', 1);
    }

    public function getApprovers()
    {
        if (!$this->activeTeamMember) {
            return collect();
        }

        return $this->activeTeamMember
            ->team
            ->getApprovers();
    }

    public function attendanceLogs()
    {
        return $this->hasMany(
            AttendanceLog::class
        );
    }
}
