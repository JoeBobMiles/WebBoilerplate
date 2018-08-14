<?php

namespace Router;

class Route
{

    /**
     * The uri this Route is invoked by.
     *
     * @var string
     */
    private $uri = '';

    /**
     * The operation this Route executes.
     *
     * @var callable
     */
    private $operation;

    public function __construct($uri, $operation)
    {
        $this->uri = $uri;
        $this->operation = $operation;
    }

    /**
     * Executes this Route with the data from the given $request_uri, and
     * returns the result of the Route's operation.
     *
     * @param  string $request_uri
     * @return mixed
     */
    public function __invoke($request_uri)
    {
        return call_user_func($this->operation, $request_uri);
    }
}