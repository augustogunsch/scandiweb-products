<?php
namespace ProductList\Http;

class Request
{
    private $method;
    private $uri;

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function __construct(array $params)
    {
        $this->uri = basename($params['REQUEST_URI']);
        $this->method = $params['REQUEST_METHOD'];
    }
}
