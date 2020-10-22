<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Tests\Entity;

use Cycle\Annotated\Annotation as Cycle;

/**
 * @Cycle\Entity(table="users")
 */
class User
{
    /**
     * @Cycle\Column(type="int", primary=true)
     */
    public ?int $id = null;

    /**
     * @Cycle\Column(type="string")
     */
    public string $name;

    /**
     * @Cycle\Relation\RefersTo(target="User", innerKey="manager_id", outerKey="id")
     */
    public ?User $manager = null;
}