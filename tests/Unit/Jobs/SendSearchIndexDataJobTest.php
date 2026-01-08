<?php

namespace Tests\Unit\Jobs;

use App\Entities\Contracts\SearchableSourceContract;
use App\Jobs\SendSearchIndexDataJob;
use App\Mail\SearchIndexDataMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class SendSearchIndexDataJobTest extends TestCase
{
    public function test_handle_sends_email_with_correct_data()
    {
        $indexName = 'any_index_name';

        $items = new Collection(Mockery::mock(SearchableSourceContract::class));

        $mailerMock = Mockery::mock(Mailer::class);
        $mailerMock->shouldReceive('to')
            ->once()
            ->with(config('mail.admin_email_address'))
            ->andReturnSelf();

        $mailerMock->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (Mailable $mail) use ($items, $indexName): bool {
                return $mail instanceof SearchIndexDataMail
                    && $mail->items === $items
                    && $mail->itemsCount === $items->count()
                    && $mail->indexName === $indexName;
            }));

        $job = new SendSearchIndexDataJob($items, $indexName);

        $job->handle($mailerMock);
    }
}
