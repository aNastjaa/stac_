<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = ['name'];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($role) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (!$role->id) {
                $role->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the users associated with the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
