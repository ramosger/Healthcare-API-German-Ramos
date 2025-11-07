<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AllExistIn implements ValidationRule
{
    /** @var Builder<Model> */
    private readonly Builder $query;

    /**
     * @param Model|Builder<Model>|class-string<Model> $modelOrQuery
     */
    public function __construct(
        Model|Builder|string $modelOrQuery,
        private readonly string $column = 'id',
    ) {
        if ($modelOrQuery instanceof Builder) {
            /** @var Builder<Model>*/
            $q = $modelOrQuery;
            $this->query = $q;

            return;
        }

        if ($modelOrQuery instanceof Model) {
            /** @var Builder<Model>*/
            $q = $modelOrQuery->newQuery();
            $this->query = $q;

            return;
        }

        $model = new $modelOrQuery();

        /** @var Builder<Model>*/
        $q = $model->newQuery();

        $this->query = $q;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var list<mixed> $value */
        $ids = collect($value)
            ->map($this->normalizeInt(...))
            ->filter(static fn ($v): bool => is_int($v))
            ->unique()
            ->values();

        /** @var Builder<Model> $query */
        $query = clone $this->query;

        /** @var Collection<int, int|string> $rawFound */
        $rawFound = $query
            ->whereIn($this->column, $ids->all())
            ->pluck($this->column);

        $rawFound
            ->map($this->normalizeInt(...))
            ->filter(static fn ($v): bool => is_int($v))
            ->values();
    }

    private function normalizeInt(mixed $v): int|null
    {
        if (is_int($v)) {
            return $v;
        }

        if (is_string($v) && $v !== '' && ctype_digit($v)) {
            return (int) $v;
        }

        return null;
    }
}
