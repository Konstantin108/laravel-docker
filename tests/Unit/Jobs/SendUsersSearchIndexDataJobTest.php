<?php

namespace Tests\Unit\Jobs;

use App\Entities\User\UserEnriched;
use App\Jobs\SendUsersSearchIndexDataJob;
use App\Mail\UsersSearchIndexDataMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class SendUsersSearchIndexDataJobTest extends TestCase
{
    public function test_handle_sends_email_with_correct_data()
    {
        $indexName = 'users';

        $users = new Collection(
            new UserEnriched(
                id: 1,
                name: 'Ivan',
                email: 'ivan@example.com',
                reserveEmail: 'ivan2@example.com',
                phone: '79091234567',
                telegram: '@ivan',
                emailVerifiedAt: Carbon::now(),
                createdAt: Carbon::now(),
                updatedAt: Carbon::now(),
            )
        );

        $mailerMock = Mockery::mock(Mailer::class);
        $mailerMock->shouldReceive('to')
            ->once()
            ->with(config('mail.admin_email_address'))
            ->andReturnSelf();

        $mailerMock->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($mail) use ($users, $indexName) {
                return $mail instanceof UsersSearchIndexDataMail
                    && $mail->users === $users
                    && $mail->usersCount === $users->count()
                    && $mail->indexName === $indexName;
            }));

        $job = new SendUsersSearchIndexDataJob($users, $indexName);

        $job->handle($mailerMock);
    }
}
