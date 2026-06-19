<?php

namespace app\middleware;

interface MiddlewareInterface {
    /**
     * @return bool
     */
    public function handle(): bool;
}