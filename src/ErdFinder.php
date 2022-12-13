<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Collection;

class ErdFinder
{
    private AbstractSchemaManager $schemaManager;
    private ModelFinder $modelFinder;
    private RelationFinder $relationFinder;
    private string $directory;

    public function __construct(
        AbstractSchemaManager $schemaManager,
        ModelFinder $modelFinder,
        RelationFinder $relationFinder
    ) {
        $this->schemaManager = $schemaManager;
        $this->modelFinder = $modelFinder;
        $this->relationFinder = $relationFinder;
    }

    public function in(string $directory): ErdFinder
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @param  string|string[]  $patterns
     * @param  string|string[]  $excludes
     * @return array
     * @throws DBALException
     */
    public function find($patterns = '*.php', array $excludes = []): array
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $patterns);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function findByFile($file, array $excludes = []): array
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $file);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function findByModel($className, array $excludes = []): array
    {
        return $this->findByModels(collect($className), $excludes);
    }

    /**
     * @param  Collection  $models
     * @param  string|string[]  $excludes
     * @return array
     * @throws DBALException
     */
    private function findByModels(Collection $models, $excludes = []): array
    {
        /** @var Collection $missing */
        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model))
            ->map(fn(Relation $relation) => $relation->related())
            ->diff($models);

        /** @var Collection $uniqueMerged */
        $relationships = $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->relationships())
            ->when(count($excludes) > 0, function (Collection $relationships) use ($excludes) {
                return $relationships->filter(fn(Relationship $relationship) => !$relationship->includes($excludes));
            })
            ->values();

        $tables = $relationships
            ->flatMap(fn(Relationship $relationship) => [$relationship->localKey(), $relationship->foreignKey()])
            ->map(fn(string $key) => Helpers::getTableName($key))
            ->unique()
            ->sort()
            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
            ->values();

        return ['tables' => $tables, 'relationships' => $relationships];
    }
}