<?php

namespace Tests\Integration\Mail\Elasticsearch;

use App\Mail\SearchIndexDataMail;
use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeSearchableSource;
use Tests\TestCase;

final class SearchIndexDataMailTest extends TestCase
{
    #[Test]
    public function it_renders_search_index_data_mail(): void
    {
        $indexName = 'any_index_name';
        $data = [
            ['id' => 1, 'name' => 'Gab', 'created_at' => null, 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Bob', 'created_at' => Carbon::now(), 'updated_at' => null],
        ];

        $items = new Collection(array_map(static function (array $item): SearchableSourceContract {
            return new FakeSearchableSource(
                id: $item['id'],
                name: $item['name'],
                createdAt: $item['created_at'],
                updatedAt: $item['updated_at'],
            );
        }, $data));

        $mail = new SearchIndexDataMail(
            items: $items,
            itemsCount: $items->count(),
            indexName: $indexName,
        );

        $mail->assertHasSubject(sprintf('Индекс %s заполнен', mb_strtoupper($indexName)));
        $mail->assertSeeInHtml((string) $items->count());

        foreach ($data as $item) {
            $mail->assertSeeInHtml((string) $item['id']);
            $mail->assertSeeInHtml($item['name']);

            if ($item['created_at'] !== null) {
                $mail->assertSeeInHtml($item['created_at']->toAtomString());
            }

            if ($item['updated_at'] !== null) {
                $mail->assertSeeInHtml($item['updated_at']->toAtomString());
            }
        }
    }
}
