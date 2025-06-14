<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function workSchedule()
    {
        return $this->hasOne(WorkSchedule::class);
    }

    public function getJWTIdentifier() 
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims() 
    {
        return [];
    }

    public function isAdmin() {
        return $this->role == 'admin';
    }

    public static function createUser(Request $request) {
        DB::beginTransaction(); 
    
        try {
            
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);
    
            if (!$user) {
                throw new \Exception("User creation failed.");
            }
    
            $workSchedule = WorkSchedule::create([
                'user_id' => $user->id,
                'schedule_type' => $request->get('schedule_type'),
                'morning_clock_in' => $request->get('morning_clock_in'),
                'morning_clock_out' => $request->get('morning_clock_out'),
                'afternoon_clock_in' => $request->get('afternoon_clock_in'),
                'afternoon_clock_out' => $request->get('afternoon_clock_out'),
                'interval' => (int) $request->get('interval'),
            ]);
    
            if (!$workSchedule) {
                throw new \Exception("Work schedule creation failed.");
            }
    
            DB::commit();
            return $user;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function getAllUsers($perPage) {
        return User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'work_schedules.schedule_type',
            'work_schedules.interval',
            'work_schedules.morning_clock_in',
            'work_schedules.morning_clock_out',
            'work_schedules.afternoon_clock_in',
            'work_schedules.afternoon_clock_out'
        )
        ->join('work_schedules', 'work_schedules.user_id', '=', 'users.id')
        ->paginate($perPage);
    }

    public static function getUser($id) {
       return User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'work_schedules.schedule_type',
            'work_schedules.interval',
            'work_schedules.morning_clock_in',
            'work_schedules.morning_clock_out',
            'work_schedules.afternoon_clock_in',
            'work_schedules.afternoon_clock_out'
        )
        ->join('work_schedules', 'work_schedules.user_id', '=', 'users.id')
        ->where('users.id', $id)
        ->firstOrFail();
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
