<?php

namespace WAFWork\Core;

class Container
{
    /**
     * The container's bindings
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's singletons
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Bind a service to the container
     *
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Bind a shared instance to the container
     *
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as shared in the container
     *
     * @param string $abstract
     * @param mixed $instance
     * @return mixed
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /**
     * Resolve an instance from the container
     *
     * @param string $abstract
     * @return mixed
     */
    public function resolve($abstract)
    {
        // If we have a shared instance, return it
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If the type has no binding, just create it
        $concrete = $abstract;

        // If we have a binding for the abstract type, get the concrete type
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract]['concrete'];
        }

        // If the concrete type is a closure, resolve it
        if ($concrete instanceof \Closure) {
            $instance = $concrete();
        } else {
            $instance = $this->build($concrete);
        }

        // If the type is marked as shared, store the instance
        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build a concrete instance of a class
     *
     * @param string $concrete
     * @return mixed
     */
    protected function build($concrete)
    {
        // If the concrete type is a string, create an instance
        if (is_string($concrete)) {
            return new $concrete();
        }

        return $concrete;
    }
} 