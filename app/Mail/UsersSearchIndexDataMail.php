<?php

namespace App\Mail;

use App\Dto\User\UserEnrichedDto;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;

class UsersSearchIndexDataMail extends Mailable
{
    public function __construct(
        /** @var Collection<int, UserEnrichedDto> */
        public readonly Collection $users,
        public readonly int $usersCount,
        public readonly string $indexName
    ) {}

    public function build(): UsersSearchIndexDataMail
    {
        return $this
            ->subject(sprintf(
                'Индекс %s заполнен',
                strtoupper($this->indexName)
            ))
            ->view('mail.users_search_index_data');
    }
}
