<?php

namespace App\Console;

abstract class Command
{
    /**
     * List of question to be prompted.
     *
     * @var array
     */
    protected array $question = [];

    /**
     * List of input from user.
     *
     * @var array
     */
    protected array $input = [];

    /**
     * Handler of CLI logic.
     *
     * @return void
     */
    public function start(): void
    {
        foreach ($this->question as $key => $question) {
            if (in_array($key, array_keys($this->input))) {
                continue;
            }
            $this->prompt($question);
            $this->setInput($key);
        }
        $this->process();
        $this->exit();
    }

    /**
     * Set input from user.
     *
     * @param  string  $key Key of question
     * @return void
     */
    protected function setInput(string $key): void
    {
        $input = trim(readline());
        if ($this->isEmpty($input)) {
            $this->retry();
        }
        $this->input[$key] = $input;
    }

    /**
     * Retry prompt question if input empty.
     *
     * @return void
     */
    protected function retry(): void
    {
        $this->prompt('Your input is empty, please try again');
        $this->start();
    }

    /**
     * Check if input is empty.
     *
     * @param  string  $input  Input from user
     * @return bool
     */
    protected function isEmpty(string $input): bool
    {
        return empty($input);
    }

    /**
     * Exit command line execution.
     *
     * @return void
     */
    protected function exit(): void
    {
        $this->prompt();
        $this->prompt('Thank you for using our service');
        exit();
    }

    /**
     * Echo string to shell.
     *
     * @param  string  $line  Line to echo
     * @param  array  $data  Data to replace placeholder in $line
     * @return void
     */
    protected function prompt(string $line = '', array $data = []): void
    {
        echo vsprintf($line, $data) . PHP_EOL;
    }

    /**
     * Process logic of input from user.
     *
     * @return void
     */
    abstract protected function process(): void;
}
