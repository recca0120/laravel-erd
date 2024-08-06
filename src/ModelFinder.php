<?php

namespace Recca0120\LaravelErd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class ModelFinder
{
    private ?string $connection;

    private Parser $parser;

    public function __construct(?string $connection = null)
    {
        $this->connection = $connection;
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }

    /**
     * @param  string|string[]  $patterns
     */
    public function find(string $directory, $patterns = '*.php'): Collection
    {
        $files = Finder::create()->files()->name($patterns)->in($directory);

        return collect($files)
            ->map(fn (SplFileInfo $file) => $this->getFullyQualifiedClassName($file))
            ->filter(fn (?string $className) => $className && self::isEloquentModel($className))
            ->filter(fn (string $className) => (new $className())->getConnectionName() === $this->connection)
            ->values();
    }

    private static function isEloquentModel(string $className): bool
    {
        try {
            return $className &&
                is_subclass_of($className, Model::class) &&
                ! (new ReflectionClass($className))->isAbstract();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function getFullyQualifiedClassName(SplFileInfo $file): ?string
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodes = $nodeTraverser->traverse($this->parser->parse($file->getContents()));

        /** @var ?Namespace_ $rootNode */
        $rootNode = collect($nodes)->first(fn (Node $node) => $node instanceof Namespace_);

        return ! $rootNode
            ? null
            : collect($rootNode->stmts)
                ->filter(static fn (Stmt $stmt) => $stmt instanceof Class_)
                ->map(static fn (Class_ $stmt) => $stmt->namespacedName->toString())
                ->first();
    }
}
