<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Http;

use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CleanOrmHeapMiddleware implements MiddlewareInterface
{
    private ContainerInterface $container;
    private ?ORMInterface $orm = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Lazy ORM resolving to prevent ORM resolution in the master process (when it compiles HTTP routes)
        if ($this->orm === null) {
            $this->orm = $this->container->get(ORMInterface::class);
        }

        // cleaning ORM heap before each request
        $this->orm->getHeap()->clean();

        return $handler->handle($request);
    }
}
