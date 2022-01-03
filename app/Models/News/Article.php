<?php

namespace App\Models\News;

use App\Models\News\NewsRating\ArticleDislike;
use App\Models\News\NewsRating\ArticleLike;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable  = [
        'title',
        'category_id',
        'user_id',
        'content_html',
        'refs',
        'country',
        'country_code',
        'author_pubkey',
        'signature'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Article belongs to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Article belongs to the author of article (user).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Article has likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    /**
     * Article has dislikes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dislikes()
    {
        return $this->hasMany(ArticleDislike::class);
    }

    /**
     * Accessor to get date in diffForHumans format.
     * >>> 2 hours ago...
     *
     * @return string
     */
    public function getCreatedAtForHumansAttribute()
    {
        $date = $this->created_at->diffForHumans();

        return $date;
    }

    /**
     * Accessor to get date in correct format.
     * >>> 22 Nov 2022 07:45 (UTC)
     *
     * @return string
     */
    public function getCreatedAtFormattedAttribute()
    {
        $date = ltrim($this->created_at->format('d M Y H:i'), 0);

        return $date . ' (UTC)';
    }

    /**
     * Accessor to find out if the post can be edited now.
     *
     * @return string
     */
    public function getIsEditableAttribute()
    {
        $createdTime = Carbon::parse($this->created_at);
        $currentTime = Carbon::now();

        $difference = $currentTime->diffInHours($createdTime, true);
        $maxTimeToEdit = intval(config('articles.max_article_editing_minutes')); // in minutes; default: 240

        if ($difference < $maxTimeToEdit) {
            return 1;
        }
        return 0;
    }
}
