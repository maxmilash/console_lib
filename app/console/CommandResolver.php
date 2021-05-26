<?php


namespace app\console;


use app\console\exception\CommandNotFoundException;

/**
 * Class CommandResolver
 * @package app\console
 */
class CommandResolver
{
    /**
     * @var array $commands Список зарегистрированных команд
     */
    private array $commands;


    /**
     * @param string $name Имя команды
     * @param callable $callback Обработчик команды
     * @param string $description Описание команды
     * @return static
     */
    public function register(string $name, callable $callback, string $description = ''): static
    {
        return $this->addCommand($name, $callback, $description);
    }

    /**
     * @param string[] $argv
     * @throws exception\ParseException
     * @throws CommandNotFoundException
     */
    public function resolve(array $argv)
    {
        $context = $this->parse($argv);
        $callback = $this->findCommand($context);
        $this->executeCommand($callback, $context);
    }

    /**
     * @param string $name
     * @param callable $callback
     * @param string $description
     * @return static
     */
    private function addCommand(string $name, callable $callback, string $description = ''): static
    {
        $this->commands[$name] = [
            'method' => $callback,
            'description' => $description,
        ];

        return $this;
    }

    /**
     * @param string[] $argv
     * @return CommandContext
     * @throws exception\ParseException
     */
    private function parse(array $argv): CommandContext
    {
        return (new CommandParser($argv))
            ->parse();
    }

    /**
     * @param CommandContext $context
     * @return callable
     * @throws CommandNotFoundException
     */
    private function findCommand(CommandContext $context): callable
    {
        $commandName = $context->getCommand();
        if (!$commandName) {
            return [$this, 'listCommand'];
        }

        if (in_array('help', $context->getArguments())) {
            return [$this, 'printHelp'];
        }

        if (isset($this->commands[$commandName])) {
            return $this->commands[$commandName]['method'];
        }

        throw new CommandNotFoundException();
    }

    /**
     * @param callable $callback
     * @param CommandContext $context
     * @return void
     */
    private function executeCommand(callable $callback, CommandContext $context): void
    {
        $callback($context);
    }

    /**
     * Выводит список всех досутпных команд
     */
    private function listCommand()
    {
        foreach ($this->commands as $name => $command) {
            Console::stdout("$name: {$command['description']} \n");
        }
    }

    /**
     * Выводит описание команды
     * @param CommandContext $context
     * @throws CommandNotFoundException
     */
    private function printHelp(CommandContext $context)
    {
        if (!isset($this->commands[$context->getCommand()])) {
            throw new CommandNotFoundException();
        }

        Console::stdout($this->commands[$context->getCommand()]['description'] . "\n");
    }
}