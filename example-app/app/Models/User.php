<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phoneNumbers',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    public $timestamps = false;
}
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     *
     *   protected $casts = [
    'email_verified_at' => 'datetime',
    ];
    }
     */


