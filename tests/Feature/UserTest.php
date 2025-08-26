<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // TODO kpstya добавить различные тесты (empty response, per_page...)

    public function test_index()
    {
        $count = 3;
        User::factory()->count($count)->create();

        $response = $this
            ->get('api/v1/user/')
            ->assertOk()
            ->assertJsonPath('meta.total', $count)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'reserve_email',
                        'phone',
                        'telegram',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'active',
                            'label',
                            'url',
                        ],
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $this->assertCount($count, $response->json('data'));
    }
}
