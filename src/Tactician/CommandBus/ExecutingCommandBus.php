<?php

namespace Tactician\CommandBus;

use Tactician\Exception\CanNotInvokeHandlerException;
use Tactician\Handler\MethodNameInflector\MethodNameInflector;
use Tactician\Handler\Locator\HandlerLocator;

/**
 * The "core" CommandBus. Locates the appropriate handler and executes command.
 */
class ExecutingCommandBus implements CommandBus
{
    /**
     * @var HandlerLocator
     */
    private $handlerLocator;

    /**
     * @var MethodNameInflector
     */
    private $methodNameInflector;

    /**
     * @param HandlerLocator $handlerLoader
     * @param MethodNameInflector $methodNameInflector
     */
    public function __construct(HandlerLocator $handlerLoader, MethodNameInflector $methodNameInflector)
    {
        $this->handlerLocator = $handlerLoader;
        $this->methodNameInflector = $methodNameInflector;
    }

    /**
     * Executes a command and optionally returns a value
     *
     * @throws CanNotInvokeHandlerException
     * @param object $command
     * @return mixed
     */
    public function execute($command)
    {
        $handler = $this->handlerLocator->getHandlerForCommand($command);
        $methodName = $this->methodNameInflector->inflect($command, $handler);

        // is_callable is used here instead of method_exists, as method_exists
        // will fail when given a handler that relies on __call.
        if (!is_callable([$handler, $methodName])) {
            throw CanNotInvokeHandlerException::onObject($command, "Method '{$methodName}' does not exist on handler");
        }

        return $handler->{$methodName}($command);
    }
}
