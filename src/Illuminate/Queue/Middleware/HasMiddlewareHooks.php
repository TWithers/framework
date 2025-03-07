<?php

namespace Illuminate\Queue\Middleware;

trait HasMiddlewareHooks
{
    /**
     * The wrapper we are creating for this middleware.
     */
    protected ?WrappedMiddleware $hookWrapper = null;

    /**
     * Adds a hook to the middleware and returns a wrapped middleware instance.
     * The hook should accept the job instance as its only argument.
     * Supported hooks are `before`, `after`, and `onFail`.
     *
     * @throws \BadMethodCallException
     */
    public function addHook(string $hookType, callable $hook): WrappedMiddleware
    {
        return $this->getHookWrapper()->$hookType($hook);
    }

    /**
     * Initializes and returns the middleware wrapper.
     */
    protected function getHookWrapper(): WrappedMiddleware
    {
        if (! $this->hookWrapper) {
            $this->hookWrapper = new WrappedMiddleware($this);
        }

        return $this->hookWrapper;
    }

    /**
     * Registers a callback to be executed before the middleware is handled.
     * If the callback returns false or releases or fails the job, the middleware will be aborted.
     * The callback should accept the job instance as its only argument.
     */
    public function before(callable $callback): WrappedMiddleware
    {
        return $this->addHook('before', $callback);
    }

    /**
     * Registers a callback to be executed after the middleware is handled, but before `$next` is called.
     * The callback should accept the job instance as its only argument.
     */
    public function after(callable $callback): WrappedMiddleware
    {
        return $this->addHook('after', $callback);
    }

    /**
     * Registers a callback to be executed if the middleware does not call `$next` and fails.
     * The callback should accept the job instance as its only argument.
     */
    public function onFail(callable $callback): WrappedMiddleware
    {
        return $this->addHook('onFail', $callback);
    }

    /**
     * Same as `after`, just a more semantic name.
     */
    public function onPass(callable $callback): WrappedMiddleware
    {
        return $this->addHook('after', $callback);
    }

    /**
     * When condition is evaluated as false, the middleware will be skipped.
     */
    public function skipUnless(callable|bool $condition): WrappedMiddleware
    {
        return $this->getHookWrapper()->skipUnless($condition);
    }

    /**
     * When condition is evaluated as true, the middleware will be skipped.
     */
    public function skipWhen(callable|bool $condition): WrappedMiddleware
    {
        return $this->getHookWrapper()->skipWhen($condition);
    }
}
