<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Collection;

class ErdGo
{
    private AbstractSchemaManager $schemaManager;
    private ModelFinder $modelFinder;
    private RelationFinder $relationFinder;
    private Template $template;
    private string $directory;

    public function __construct(
        AbstractSchemaManager $schemaManager,
        ModelFinder $modelFinder,
        RelationFinder $relationFinder
    ) {
        $this->schemaManager = $schemaManager;
        $this->modelFinder = $modelFinder;
        $this->relationFinder = $relationFinder;
        $this->template = new Template();
    }

    public function in(string $directory): ErdGo
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @param  string|string[]  $patterns
     * @param  string|string[]  $excludes
     * @return string
     * @throws DBALException
     */
    public function generate($patterns = '*.php', array $excludes = []): string
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $patterns);

        return $this->generateByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function generateByFile($file, array $excludes = []): string
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $file);

        return $this->generateByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function generateByModel($className, array $excludes = []): string
    {
        return $this->generateByModels(collect($className), $excludes);
    }

    /**
     * @param  Collection  $models
     * @param  string|string[]  $excludes
     * @return string
     * @throws DBALException
     */
    private function generateByModels(Collection $models, $excludes = []): string
    {
        /** @var Collection $missing */
        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model))
            ->map(fn(Relation $relation) => $relation->related())
            ->diff($models);

        /** @var Collection $relationships */
        $relationships = $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->relationships())
            ->when(count($excludes) > 0, function (Collection $relationships) use ($excludes) {
                return $relationships->filter(fn(Relationship $relationship) => !$relationship->includes($excludes));
            });

        /** @var Collection $tables */
        $tables = $relationships
            ->flatMap(fn(Relationship $relationship) => [$relationship->localKey(), $relationship->foreignKey()])
            ->map(fn(string $key) => Helpers::getTableName($key))
            ->unique()
            ->sortBy(fn(string $table) => $table)
            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
            ->map(fn(Table $table): string => $this->template->renderTable($table));

        $relationships = $relationships
            ->unique(fn(Relationship $relationship) => $relationship->hash())
            ->map(fn(Relationship $relationship) => $this->template->renderRelationship($relationship))
            ->unique()
            ->sortBy(fn(string $line) => $line);

        return $tables->merge($relationships)->implode("\n");
    }
}