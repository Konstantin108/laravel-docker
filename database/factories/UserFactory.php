<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function withName(string $name): self
    {
        return $this->state(fn (): array => ['name' => $name]);
    }

    public function withEmail(string $email): self
    {
        return $this->state(fn (): array => ['email' => $email]);
    }

    public function hasContact(Contact|ContactFactory|null $contact = null): self
    {
        if ($contact instanceof Contact) {
            return $this->afterCreating(static function (User $user) use ($contact): void {
                $user->contact()->save($contact);
            });
        }

        return $this->has($contact ?? Contact::factory());
    }
}
