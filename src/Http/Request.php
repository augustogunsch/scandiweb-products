<?php
namespace ProductList\Http;

class Request
{
    private $method;
    private $uri;
    private $queryParams;
    private $formParams;

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getFormParams()
    {
        return $this->formParams;
    }

    public function __construct(array $params, array $queryParams, array $formParams)
    {
        $uri_base = strtok($params['REQUEST_URI'], '?');
        $uri_base = trim(urldecode($uri_base), '/');
        $this->uri = explode('/', $uri_base);

        $this->method = $params['REQUEST_METHOD'];

        $this->queryParams = $queryParams;
        $this->formParams = $formParams;
    }
}
