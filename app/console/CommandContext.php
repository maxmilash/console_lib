<?php


namespace app\console;


/**
 * Class CommandContext
 * @package app\console
 */
class CommandContext
{
    /** @var string $command */
    private string $command;

    /** @var string[] $arguments */
    private array $arguments;

    /** @var array $parameters */
    private array $parameters;


    /**
     * CommandContext constructor.
     * @param string $command
     * @param string[] $arguments
     * @param array $parameters
     */
    public function __construct(string $command, array $arguments = [], array $parameters = [])
    {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}