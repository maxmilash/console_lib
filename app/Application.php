<?php


namespace app;


use app\console\CommandContext;
use app\console\CommandResolver;
use app\console\Console;
use app\console\exception\CommandNotFoundException;
use app\console\exception\ParseException;

/**
 * Class Application
 * @package app
 */
class Application
{

    /**
     * @param string[] $argv
     */
    public function run(array $argv)
    {
        try {
            $resolver = new CommandResolver();
            $resolver->register(
                'command_name',
                [$this, 'printSomeSelf'],
                'Write all arguments and parameters'
            );

            $resolver->resolve($argv);

        } catch (ParseException $e) {
            Console::stdout("Ошибка при разборе команды \n");
        } catch (CommandNotFoundException $e) {
            Console::stdout("Команды не существует \n");
        }
    }

    /**
     * @param CommandContext $context
     */
    public function printSomeSelf(CommandContext $context)
    {
        Console::stdout("Called command: {$context->getCommand()} \n");

        if ($context->getArguments()) {
            Console::stdout("\n Arguments: \n");
            foreach ($context->getArguments() as $parameter) {
                Console::stdout("\t- $parameter \n");
            }
        }

        if ($context->getParameters()) {
            Console::stdout("\n Options: \n");
            foreach ($context->getParameters() as $name => $values) {
                Console::stdout("\t- $name \n");
                foreach ($values as $value) {
                    Console::stdout("\t\t - $value \n");
                }
            }
        }
    }
}