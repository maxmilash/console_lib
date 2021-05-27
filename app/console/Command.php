<?php


namespace app\console;


/**
 * Class Command
 * @package app\console
 */
class Command
{
    /** @var string $name */
    private string $name;

    /** @var callable $method */
    private $method;

    /** @var string $description */
    private string $description;


    public function __construct(string $name, callable $method, string $description = '')
    {
        $this->name = $name;
        $this->method = $method;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getMethod(): callable
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}