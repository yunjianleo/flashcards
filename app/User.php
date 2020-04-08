<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, Sluggable, HasPushSubscriptions, HasMediaTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::updating(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = bcrypt($user->password);
            }
        });

        parent::boot();
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first() ?? $this->where('email', $username)->first();
    }

    public function sluggable()
    {
        return [
            'username' => [
                'unique' => true,
                'separator' => '',
                'onUpdate' => false,
            ],
        ];
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('main')
            ->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/webp'])
            ->singleFile();
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    public function scopeNewest($query)
    {
        return $query->latest();
    }

    public function cards()
    {
        return $this->hasManyThrough('App\Card', 'App\Deck');
    }

    public function decks()
    {
        return $this->hasMany('App\Deck');
    }

    public function userDecks()
    {
        return $this->hasMany('App\Deck')->withoutGlobalScopes();
    }

    public function publishedCards()
    {
        return $this->cards()->published();
    }
}
