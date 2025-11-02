<?php

namespace App\Models;

use App\Models\Contracts\SearchableContract;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Contact
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $phone
 * @property string $telegram
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static ContactFactory factory($count = null, $state = [])
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
