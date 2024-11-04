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

    // Ensure the key type is UUID
    protected $keyType = 'string';

    // Disable auto-incrementing
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($role) {
            // Generate a UUID for the primary key 'id'
            if (empty($role->id)) {
                $role->id = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
