<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM;

use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\Heap\Node;
use IteratorAggregate;
use SplObjectStorage;
use Swoole\Coroutine;

final class CoroutineHeap implements HeapInterface, IteratorAggregate
{
    private const CTX_CYCLE_ORM_HEAP = 'cycle_orm_heap';

    private SplObjectStorage $emptyIterator;

    /**
     * CoroutineHeap constructor.
     */
    public function __construct()
    {
        $this->emptyIterator = new SplObjectStorage();
    }

    /**
     * CoroutineHeap destructor.
     */
    public function __destruct()
    {
    }

    private function createHeap(): HeapInterface
    {
        $heap = new Heap();

        Coroutine::getContext()[self::CTX_CYCLE_ORM_HEAP] = $heap;

        return $heap;
    }

    private function getHeapFromContext(): ?HeapInterface
    {
        return Coroutine::getContext()[self::CTX_CYCLE_ORM_HEAP] ?? null;
    }

    /**
     * @return SplObjectStorage
     */
    public function getIterator(): SplObjectStorage
    {
        $heap = $this->getHeapFromContext();

        return $heap ? $heap->getIterator() : $this->emptyIterator;
    }

    /**
     * @inheritdoc
     */
    public function has($entity): bool
    {
        $heap = $this->getHeapFromContext();

        return $heap ? $heap->has($entity) : false;
    }

    /**
     * @inheritdoc
     */
    public function get($entity): ?Node
    {
        $heap = $this->getHeapFromContext();

        return $heap ? $heap->get($entity) : null;
    }

    /**
     * @inheritdoc
     */
    public function find(string $role, array $scope)
    {
        $heap = $this->getHeapFromContext();

        return $heap ? $heap->find($role, $scope) : null;
    }

    /**
     * @inheritdoc
     */
    public function attach($entity, Node $node, array $index = []): void
    {
        $heap = $this->getHeapFromContext();
        if ($heap === null) {
            $heap = $this->createHeap();
        }

        $heap->attach($entity, $node, $index);
    }

    /**
     * @inheritdoc
     */
    public function detach($entity): void
    {
        $heap = $this->getHeapFromContext();

        if ($heap !== null) {
            $heap->detach($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public function clean(): void
    {
        $heap = $this->getHeapFromContext();
        if ($heap !== null) {
            $heap->clean();
        }
    }
}
