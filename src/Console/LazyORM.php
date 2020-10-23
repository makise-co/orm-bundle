<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\SourceInterface;
use Cycle\ORM\TransactionInterface;

class LazyORM implements ORMInterface
{
    private ?ORM $orm = null;
    private \Closure $initializer;

    public function __construct(\Closure $initializer)
    {
        $this->initializer = $initializer;
    }

    public function resolveRole($entity): string
    {
        return $this->getORM()->resolveRole($entity);
    }

    public function get(string $role, array $scope, bool $load = true)
    {
        return $this->getORM()->get($role, $scope, $load);
    }

    public function make(string $role, array $data = [], int $node = Node::NEW)
    {
        return $this->getORM()->make($role, $data, $node);
    }

    public function promise(string $role, array $scope)
    {
        return $this->getORM()->promise($role, $scope);
    }

    public function getFactory(): FactoryInterface
    {
        return $this->getORM()->getFactory();
    }

    public function getSchema(): SchemaInterface
    {
        return $this->getORM()->getSchema();
    }

    public function getHeap(): HeapInterface
    {
        return $this->getORM()->getHeap();
    }

    public function getMapper($entity): MapperInterface
    {
        return $this->getORM()->getMapper($entity);
    }

    public function getRepository($entity): RepositoryInterface
    {
        return $this->getORM()->getRepository($entity);
    }

    public function queueStore($entity, int $mode = TransactionInterface::MODE_CASCADE): ContextCarrierInterface
    {
        return $this->getORM()->queueStore($entity, $mode);
    }

    public function queueDelete($entity, int $mode = TransactionInterface::MODE_CASCADE): CommandInterface
    {
        return $this->getORM()->queueDelete($entity, $mode);
    }

    public function getSource(string $role): SourceInterface
    {
        return $this->getORM()->getSource($role);
    }

    private function getORM(): ORM
    {
        return $this->orm ?? ($this->orm = $this->initializeORM());
    }

    private function initializeORM(): ORM
    {
        return ($this->initializer)();
    }
}