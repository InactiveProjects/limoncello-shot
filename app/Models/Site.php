<?php namespace App\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int        id
 * @property string     name
 * @property Collection posts
 * @property Carbon     created_at
 * @property Carbon     updated_at
 *
 * @method static Site findOrFail(int $id)
 */
class Site extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'name',
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
