<?php

namespace App\Models;

use WAFWork\Database\Model;

class User extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Hash the password before saving
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Verify the password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }
} 