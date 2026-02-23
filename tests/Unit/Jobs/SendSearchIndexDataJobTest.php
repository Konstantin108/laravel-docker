<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendSearchIndexDataJob;
use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SendSearchIndexDataJobTest extends TestCase
{
    #[Test]
    public function it_sends_mail()
    {
        /** @var Mailer&MockInterface $mailer */
        $mailer = $this->mock(Mailer::class);
        $mailer->shouldReceive('to')
            ->once()
            ->with(config('mail.admin_email_address'))
            ->andReturnSelf();

        $mailer->shouldReceive('send')->once();

        $job = new SendSearchIndexDataJob(
            new Collection($this->mock(SearchableSourceContract::class)),
            'any_index_name'
        );

        $job->handle($mailer);
    }
}
