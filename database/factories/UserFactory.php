<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
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
            'password' => self::$password ??= Hash::make('password'),
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
