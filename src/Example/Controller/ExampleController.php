<?php

namespace Example\Controller;

use Example\Model\Example;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExampleController
{
    public function index(Request $request, $name)
    {
        $example = new Example();

        return new Response($example->sayHello($name));
    }
}
