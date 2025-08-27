<?php

declare(strict_types=1);

namespace App\Repositories\Scopes\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SearchScope
{
    private ?string $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search !== null && mb_strlen($search) > 2
            ? '%'.$search.'%'
            : null;

    }

    /**
     * @param  Builder<User>  $builder
     */
    public function __invoke(Builder $builder): void
    {
        if ($this->search === null) {
            return;
        }

        $builder->where(function (Builder $builder): void {
            $builder->where('name', 'like', $this->search)
                ->orWhere('email', 'like', $this->search)
                ->orWhereHas('contact', function (Builder $builder): void {
                    $builder->where('email', 'like', $this->search)
                        ->orWhere('phone', 'like', $this->search)
                        ->orWhere('telegram', 'like', $this->search);
                });
        });
    }
}
