<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'admin_users';
    public $timestamps = false; // table only has created_at, set by the DB

    protected $fillable = ['name', 'email', 'password_hash'];
    protected $hidden = ['password_hash'];
    protected $casts = ['password_hash' => 'hashed'];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    // admin_users has no remember_token column
    public function getRememberTokenName(): string
    {
        return '';
    }
}