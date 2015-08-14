<?php namespace App\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int        id
 * @property string     first_name
 * @property string     last_name
 * @property string     twitter
 * @property Collection posts
 * @property Carbon     created_at
 * @property Carbon     updated_at
 *
 * @method static Author findOrFail(int $id)
 */
class Author extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'twitter',
    ];

    /**
     * Get relation to posts.
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
