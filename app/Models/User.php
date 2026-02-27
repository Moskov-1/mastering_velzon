<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 


use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class User extends Authenticatable implements JWTSubject
{
    
    protected static function booted()
    {
        static::created(function ($user) {
            $user->profile()->create([
                'user_id' => $user->id
            ]);
        });
    }
    
    protected $guarded = ['id'];

    /** Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed */
    public function getJWTIdentifier(){
        return $this->getKey();
    }

    /** Return a key value array, containing any custom claims to be added to the JWT.
     * @return array */
    public function getJWTCustomClaims(){
        return [];
    }

    public static function roles(){
        return [
            'ADMIN' => env('DEFAULT_ADMIN_ROLE', 'admin'),
            'USER' => env('DEFAULT_USER_ROLE', 'user')
        ];
    } 

    public function getRole($slug)
    {
        // Get the roles array
        $roles = self::roles();

        // Convert the slug to lowercase
        $slug = strtolower($slug);

        // Check if the slug exists in the values of the roles array
        if (in_array($slug, array_values($roles))) {
            // Return the matching role key (ADMIN or USER)

            return $slug;
            // return array_search($slug, $roles);
        }

        // If not, return the default role (USER)
        return $roles['USER'];
    }
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function getAvatarAttribute($value): string | null
    {
        if (request()->is('api/*') && request()->method() === 'GET' && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }
}
