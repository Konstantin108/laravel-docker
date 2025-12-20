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
    protected static ?string $password;

    // TODO kpstya что-то не то со свойством password, возможно переделать это

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->dateTime(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    // TODO kpstya возможно убрать из User всё, что связано с авторизацией, тут это не нужно

    public function unverified(): UserFactory
    {
        return $this->state(static fn (): array => [
            'email_verified_at' => null,
        ]);
    }

    // TODO kpstya возможно нужно переделать afterCreating() на has()

    public function withContact(): UserFactory
    {
        return $this->afterCreating(
            static fn (User $user): Contact => Contact::factory()->for($user)->create()
        );
    }
}
