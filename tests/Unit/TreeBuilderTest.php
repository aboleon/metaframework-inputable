<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Tests\Unit;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use MetaFramework\Inputable\Support\TreeBuilder;
use PHPUnit\Framework\TestCase;

class TreeBuilderTest extends TestCase
{
    public function test_build_tree_returns_self_and_prints_nested_list(): void
    {
        $collection = new EloquentCollection([
            new TreeNode(1, null, 'Root'),
            new TreeNode(2, 1, 'Child'),
            new TreeNode(3, null, 'Other'),
        ]);

        $builder = new TreeBuilderHarness($collection);

        $this->assertSame($builder, $builder->buildTree());
        $this->assertSame('<ul><li>Root<ul><li>Child</li></ul></li><li>Other</li></ul>', $builder->print());
    }

    public function test_print_returns_empty_string_when_tree_is_empty(): void
    {
        $builder = new TreeBuilderHarness(new EloquentCollection);

        $builder->buildTree();

        $this->assertSame('', $builder->print());
    }
}

class TreeBuilderHarness
{
    use TreeBuilder;

    public function __construct(EloquentCollection $collection)
    {
        $this->collection = $collection;
    }
}

class TreeNode
{
    public EloquentCollection $subs;

    public function __construct(
        public int $id,
        public ?int $parent,
        public string $title
    ) {
        $this->subs = new EloquentCollection;
    }

    public function translation(string $key): string
    {
        return $this->title;
    }
}
