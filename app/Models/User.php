<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 *  App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Contact|null $contact
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<User>|User whereUpdatedAt($value)
 *
 * @mixin Model
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // TODO kpstya надо уточнить по поводу генерации документации для моделей плагином laravel-ide-helper

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasOne<Contact, $this>
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }
}
