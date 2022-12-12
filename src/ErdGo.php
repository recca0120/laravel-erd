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

    /**
     * @param  string  $directory
     * @param  string|string[]  $patterns
     * @return string
     * @throws DBALException
     */
    public function generate(string $directory, $patterns = '*.php'): string
    {
        $models = $this->modelFinder->find($directory, $patterns);

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
            ->map(fn(string $key) => $this->getTableName($key))
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

    private function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }
}