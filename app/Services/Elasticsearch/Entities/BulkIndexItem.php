<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Entities;

use App\Services\Elasticsearch\Enums\BulkStatusEnum;

final readonly class BulkIndexItem
{
    public function __construct(
        public int $id,
        public int $seqNumber,
        public string $index,
        public int $version,
        public string $result,
        public int $primaryTerm,
        public BulkStatusEnum $status,
        public ?string $type = null,
    ) {}

    /**
     * @return array<string, BulkStatusEnum|int|string|null>
     */
    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            '_seq_no' => $this->seqNumber,
            '_index' => $this->index,
            '_version' => $this->version,
            'result' => $this->result,
            '_primary_term' => $this->primaryTerm,
            'status' => $this->status,
            '_type' => $this->type,
        ];
    }

    /**
     * @param array{
     *     _index: string,
     *     _type?: string|null,
     *     _id: string,
     *     _version: int,
     *     result: string,
     *     _shards: array{
     *         total: int,
     *         successful: int,
     *         failed: int
     *     },
     *     _seq_no: int,
     *     _primary_term: int,
     *     status: int
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['_id'],
            seqNumber: $data['_seq_no'],
            index: $data['_index'],
            version: $data['_version'],
            result: $data['result'],
            primaryTerm: $data['_primary_term'],
            status: BulkStatusEnum::from($data['status']),
            type: $data['_type'] ?? null,
        );
    }
}
