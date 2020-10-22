<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Http;

use Cycle\ORM\ORM;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CleanOrmHeapMiddleware implements MiddlewareInterface
{
    private ORM $orm;

    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // cleaning ORM heap before each request
        $this->orm->getHeap()->clean();

        return $handler->handle($request);
    }
}