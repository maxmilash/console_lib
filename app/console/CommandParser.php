<?php


namespace app\console;


use app\console\exception\ParseException;

/**
 * Class CommandParser
 * @package app\console
 */
class CommandParser
{
    /** @var string[] $inputParameters */
    protected array $inputParameters;


    /**
     * CommandParser constructor.
     * @param $argv
     */
    public function __construct($argv)
    {
        array_shift($argv);//Убираем файл из которого была запущена команда
        $this->inputParameters = $argv;
    }

    /**
     * @return CommandContext
     * @throws ParseException
     */
    public function parse(): CommandContext
    {
        $inputParameters = $this->inputParameters;
        $command = $this->shiftCommandName($inputParameters);
        $arguments = $this->shiftArguments($inputParameters);
        $parameters = $this->shiftParameters($inputParameters);

        if ($inputParameters) {
            throw new ParseException();
        }

        return new CommandContext($command, $arguments, $parameters);
    }

    /**
     * @param string[] $inputParameters
     * @return string
     * @throws ParseException
     */
    private function shiftCommandName(array &$inputParameters): string
    {
        $command = preg_grep('/^(?!([\[\]{}]))(.*)/', $inputParameters);

        if (!$command) {
            if ($inputParameters) {
                throw new ParseException();
            }

            return '';
        }

        if (count($command) > 2) {
            throw new ParseException();
        }

        unset($inputParameters[key($command)]);

        return reset($command);
    }

    /**
     * @param array $inputParameters
     * @return string[]
     */
    private function shiftArguments(array &$inputParameters): array
    {
        $stringArguments = preg_grep('/^{.+?}$/', $inputParameters);
        $inputParameters = array_diff_key($inputParameters, $stringArguments);

        $arguments = [];
        foreach ($stringArguments as &$argument) {
            $argument = trim($argument, "{}");
            $arguments = array_merge($arguments, explode(',', $argument));
        }

        return $arguments;
    }

    /**
     * @param array $inputParameters
     * @return array
     * @throws ParseException
     */
    private function shiftParameters(array &$inputParameters): array
    {
        $stringParameters = preg_grep('/^\[.*?]$/', $inputParameters);
        $inputParameters = array_diff_key($inputParameters, $stringParameters);

        $parameters = [];
        foreach ($stringParameters as $parameter) {
            $parameter = trim($parameter, '[]');
            [$name, $stringValue] = explode('=', $parameter);

            $value = $this->findValueParameters($stringValue);

            if (isset($parameters[$name])) {
                $parameters[$name] = array_merge($parameters[$name], $value);
            } else {
                $parameters[$name] = $value;
            }
        }

        return $parameters;
    }

    /**
     * @param string $parameterValue
     * @return string[]
     * @throws ParseException
     */
    private function findValueParameters(string $parameterValue): array
    {
        if (!preg_match('/^{.+?}/', $parameterValue)) {
            return [$parameterValue];
        }

        if (!$this->validBrackets($parameterValue)) {
            throw new ParseException();
        }

        $parameterValue = trim($parameterValue, '{}');
        return explode(',', $parameterValue);
    }

    /**
     * @param string $value
     * @return bool
     */
    private function validBrackets(string $value): bool
    {
        return $value[0] === '{' && $value[-1] === '}';
    }
}