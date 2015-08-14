<?php namespace App\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        id
 * @property int        author_id
 * @property int        site_id
 * @property string     title
 * @property string     body
 * @property Author     author
 * @property Site       site
 * @property Collection comments
 * @property Carbon     created_at
 * @property Carbon     updated_at
 *
 * @method static Post findOrFail(int $id)
 */
class Post extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'title',
        'body',
        'author_id',
        'site_id',
    ];

    /**
     * Get relation to author.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get relation to site.
     *
     * @return BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get relation to comments.
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
