<?php

declare(strict_types=1);

namespace App\Repositories\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final readonly class LimitScope
{
    public function __construct(private ?int $limit = null) {}

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     */
    public function __invoke(Builder $builder): void
    {
        $builder->when($this->limit, function (Builder $builder): void {
            $builder->limit($this->limit);
        });
    }
}
