<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Todo
 *
 * @property int $id
 * @property int $user_id User ID For Relation
 * @property string $name
 * @property boolean $is_activate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereIsActivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Todo whereUserId($value)
 * @mixin \Eloquent
 * @property-read User $user
 */
class Todo extends Model
{
    protected $fillable = [
        'name',
        'is_activate'
    ];

    protected $casts = [
        'is_activate' => 'boolean'
    ];

    protected $hidden = [
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
