<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
    ];

    /**
     * Attributes hidden for serialization.
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
        'password' => 'hashed',
    ];

    /**
     * Relationship with Role model.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship with UserProfile model.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * Relationship with Post model.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Check if the user has a specific role by role name.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        if ($this->role) {
            Log::info('Checking role', ['role' => $this->role->name]);
            return $this->role->name === $roleName;
        }

        Log::warning('Role is undefined for user', ['user_id' => $this->id]);
        return false;
    }

    /**
     * Check if the user is an admin by role name.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Log the role of the authenticated user if available.
     */
    public static function logAuthenticatedUserRole()
    {
        $user = Auth::user();

        if ($user && $user->role) {
            Log::info('Authenticated user role', [
                'user_id' => $user->id,
                'role' => $user->role->name
            ]);
        } else {
            Log::warning('No role found for authenticated user', [
                'user_id' => $user->id ?? 'N/A',
                'user_exists' => isset($user)
            ]);
        }
    }
}
