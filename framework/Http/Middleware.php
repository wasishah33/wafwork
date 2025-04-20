<?php

namespace WAFWork\Http;

interface Middleware
{
    /**
     * Handle the request
     *
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, callable $next);
} 