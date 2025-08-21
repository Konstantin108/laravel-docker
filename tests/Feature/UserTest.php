<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        $count = 3;
        User::factory()->count($count)->create();

        $response = $this->get('api/user/')->assertOk();

        $this->assertCount($count, $response->json('data'));
        $response->assertJsonPath('meta.total', $count);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
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
    }
}
