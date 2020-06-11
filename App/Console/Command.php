<?php

namespace App\Console;

abstract class Command
{
    protected $question = [];

    protected $input = [];

    public function start()
    {
        foreach ($this->question as $key => $question) {
            if (in_array($key, $this->input)) {
                continue;
            }
            $this->prompt($question);
            $this->getInput($key);
        }
        $this->process();
        $this->exit();
    }

    protected function getInput($key)
    {
        $input = trim(readline());
        if ($this->isEmpty($input)) {
            $this->prompt('Your input is empty, please try again');
            $this->start();
            $this->exit();
        }
        $this->input[$key] = $input;
    }

    protected function isEmpty($input)
    {
        return empty($input);
    }

    protected function exit()
    {
        $this->prompt();
        $this->prompt('Thank you for using our service');
        exit();
    }

    abstract protected function process();

    protected function prompt($line = '', $data = [])
    {
        echo vsprintf($line, $data) . PHP_EOL;
    }
}
