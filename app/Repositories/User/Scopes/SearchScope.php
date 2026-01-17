<?php

declare(strict_types=1);

namespace App\Repositories\User\Scopes;

use App\Conditions\SearchCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final readonly class SearchScope
{
    public function __construct(private ?string $search = null) {}

    /**
     * @param  Builder<User>  $builder
     */
    public function __invoke(Builder $builder): void
    {
        if (! SearchCondition::isSatisfiedBy($this->search)) {
            return;
        }

        $search = '%'.$this->search.'%';

        $builder->where(function (Builder $builder) use ($search): void {
            $builder->where('name', 'like', $search)
                ->orWhere('email', 'like', $search)
                ->orWhereRelation('contact', 'email', 'like', $search)
                ->orWhereRelation('contact', 'phone', 'like', $search)
                ->orWhereRelation('contact', 'telegram', 'like', $search);
        });
    }
}
