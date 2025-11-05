<?php

namespace App\Models;

use App\Models\Contracts\SearchableContract;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $phone
 * @property string|null $telegram
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static ContactFactory factory($count = null, $state = [])
 * @method static Builder<static>|Contact newModelQuery()
 * @method static Builder<static>|Contact newQuery()
 * @method static Builder<static>|Contact query()
 * @method static Builder<static>|Contact whereCreatedAt($value)
 * @method static Builder<static>|Contact whereEmail($value)
 * @method static Builder<static>|Contact whereId($value)
 * @method static Builder<static>|Contact wherePhone($value)
 * @method static Builder<static>|Contact whereTelegram($value)
 * @method static Builder<static>|Contact whereUpdatedAt($value)
 * @method static Builder<static>|Contact whereUserId($value)
 *
 * @mixin Model
 */
class Contact extends Model implements SearchableContract
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
