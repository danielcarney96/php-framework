<?php

namespace Example\Model;

class Example
{
    public function sayHello(string $name = null)
    {
        if (!$name) {
            $name = 'World';
        }

        return sprintf("Hello %s", $name);
    }
}
