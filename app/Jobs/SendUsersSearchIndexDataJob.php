<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Entities\User\UserEnriched;
use App\Mail\UsersSearchIndexDataMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

final class SendUsersSearchIndexDataJob implements ShouldQueue
{
    // TODO kpstya проверить записи в jobs и failed_jobs, как бы я мог их перезапускать, если они упали

    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        /** @var Collection<int, UserEnriched> */
        public readonly Collection $users,
        public readonly string $indexName
    ) {}

    public function handle(Mailer $mailer): void
    {
        $mailer->to(config('mail.admin_email_address'))
            ->send(new UsersSearchIndexDataMail(
                $this->users,
                $this->users->count(),
                $this->indexName
            ));
    }
}
