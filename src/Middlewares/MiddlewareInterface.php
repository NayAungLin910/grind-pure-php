<?php

namespace Src\Middlewares;

interface MiddlewareInterface
{
    /**
     * Run security check on the client calling user
     */
    public function runSecurityCheck(): void;
}
