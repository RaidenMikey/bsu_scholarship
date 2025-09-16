<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // 👈 add this
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail // 👈 implement interface
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'campus_id', // ✅ campus assignment
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // ✅ needed for email verification
    ];

    // ✅ Relationships

    /**
     * User belongs to a campus
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * User has many applications
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * User can apply to many scholarships
     */
    public function appliedScholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'applications')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * User has one application form
     */
    public function form()
    {
        return $this->hasOne(Form::class, 'user_id');
    }
}
