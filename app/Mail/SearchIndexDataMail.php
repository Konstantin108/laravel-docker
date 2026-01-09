<?php

declare(strict_types=1);

namespace App\Mail;

use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;

final class SearchIndexDataMail extends Mailable
{
    public function __construct(
        /** @var Collection<int, SearchableSourceContract> */
        public readonly Collection $items,
        public readonly int $itemsCount,
        public readonly string $indexName
    ) {}

    public function build(): SearchIndexDataMail
    {
        return $this->subject(
            sprintf('Индекс %s заполнен', strtoupper($this->indexName))
        )
            ->view('mail.elasticsearch.search_index_filled');
    }
}
