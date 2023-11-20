<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = "users";
    protected $primaryKey = "id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_telp',
        'status',
        'image'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->id = $user->generateUserId();
        });
    }

    public function generateUserId()
    {
        $year = now()->format('y');
        $month = now()->format('m');
        $index = $this->getNextIndex();

        return "{$year}.{$month}.{$index}";
    }

    public function getNextIndex()
    {
        $latestUser = self::where('id', 'like', now()->format('y.m.%'))
            ->latest('id')
            ->first();

        if ($latestUser) {
            $index = intval(Str::afterLast($latestUser->id, '.')) + 1;
        } else {
            $index = 1;
        }

        return $index;
    }

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
        'id' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
