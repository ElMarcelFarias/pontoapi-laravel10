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
}
