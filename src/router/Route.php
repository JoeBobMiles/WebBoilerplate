<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

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

    /**
     * Creates a new instance of a Route
     *
     * @param   string      $uri       The URI this Route belongs to.
     * @param   callable    $operation The operatoin to execute for the given URI.
     *
     * @return  Route
     */
    public function __construct(string $uri, callable $operation)
    {
        $this->uri = $uri;
        $this->operation = $operation;
    }

    /**
     * Executes this Route with the data from the given $request_uri, and
     * returns the result of the Route's $operation.
     *
     * @param   string  $request_uri
     *
     * @return  mixed   Returns the result of the in built $operation.
     */
    public function __invoke(string $request_uri)
    {
        return call_user_func($this->operation, $request_uri);
    }
}
