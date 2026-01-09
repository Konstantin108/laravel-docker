<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Entities;

final readonly class BulkIndexItem
{
    private const CREATED_CODE = 201;

    private const UPDATED_CODE = 200;

    public function __construct(
        public int $id,
        public int $seqNumber,
        public string $type,
        public string $index,
        public int $version,
        public string $result,
        public int $primaryTerm,
        public int $status,
    ) {}

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            '_seq_no' => $this->seqNumber,
            '_type' => $this->type,
            '_index' => $this->index,
            '_version' => $this->version,
            'result' => $this->result,
            '_primary_term' => $this->primaryTerm,
            'status' => $this->status,
        ];
    }

    public function isCreated(): bool
    {
        return $this->status === self::CREATED_CODE;
    }

    public function isUpdated(): bool
    {
        return $this->status === self::UPDATED_CODE;
    }

    /**
     * @param array{
     *     _index: string,
     *     _type: string,
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
            type: $data['_type'],
            index: $data['_index'],
            version: $data['_version'],
            result: $data['result'],
            primaryTerm: $data['_primary_term'],
            status: $data['status'],
        );
    }
}
