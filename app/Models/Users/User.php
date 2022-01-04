<?php

namespace App\Models\Users;

use App\Models\News\NewsRating\ArticleDislike;
use App\Models\News\NewsRating\ArticleLike;
use App\Notifications\Auth\PasswordResetRequestNotification;
use App\Notifications\Auth\PasswordResetSuccessNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Model
{
    use HasApiTokens, Notifiable, Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'email_verified_at',
        'password',
        'news_num',
        'verified_news_num',
        'country',
        'country_code',
        'timezone',
        'news_confirm_notice'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'news_num' => 'integer',
        'verified_news_num' => 'integer'
    ];

    /**
     * User has articles that he liked.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    /**
     * User has articles that he disliked.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dislikes()
    {
        return $this->hasMany(ArticleDislike::class);
    }

    /**
     * Accessor to get created date in correct format.
     * >>> 22 Nov 2022
     *
     * @return string
     */
    public function getCreatedAtFormattedAttribute()
    {
        $date = ltrim($this->created_at->format('d M Y'), 0);

        return $date;
    }

    /**
     *  Queued sendApiEmailVerificationNotification
     *
     *  Notification about registration on user's email.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     *  Queued sendEmailPasswordResetRequest
     *
     *  Notification about REQUEST of password reset.
     *
     *  @param  string  $token
     */
    public function sendEmailPasswordResetRequest($token)
    {
        $this->notify(new PasswordResetRequestNotification($token));
    }

    /**
     *  Queued sendEmailPasswordResetSuccess
     *
     *  Notification about SUCCESSFUL password reset.
     */
    public function sendEmailPasswordResetSuccess()
    {
        $this->notify(new PasswordResetSuccessNotification());
    }

}
