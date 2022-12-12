<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

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
     * @return string
     * @throws DBALException
     */
    public function generate($patterns = '*.php'): string
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $patterns);

        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model))
            ->map(fn(Relation $relation) => $relation->related())
            ->diff($models);

        $relations = $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->relationships());

        $tables = $relations
            ->flatMap(fn(Relationship $drawer) => [$drawer->localKey(), $drawer->foreignKey()])
            ->map(fn(string $key) => Helpers::getTableName($key))
            ->sort()
            ->unique()
            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
            ->map(fn(Table $table): string => $this->template->renderTable($table));

        $relationships = $relations
            ->unique(fn(Relationship $relationship) => $relationship->hash())
            ->map(fn(Relationship $relationship) => $this->template->renderRelationship($relationship))
            ->sort()
            ->unique();

        return $tables->merge($relationships)->implode("\n");
    }
}