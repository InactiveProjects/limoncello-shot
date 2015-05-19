<?php namespace App\Models\Eloquent;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package Neomerx\Tests\JsonApi
 *
 * @property int    id
 * @property int    post_id
 * @property string body
 * @property Post   post
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @method static Comment findOrFail(int $id)
 */
class Comment extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'body',
        'post_id',
    ];

    /**
     * Get relation to post.
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
