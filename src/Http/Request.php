<?php
namespace ProductList\Http;

class Request
{
    private $method;
    private $uri;
    private $queryString;

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getQueryString()
    {
        return $this->queryString;
    }

    public function __construct(array $params)
    {
        $uri_base = trim($params['REQUEST_URI'], '?'.$params['QUERY_STRING']);
        $uri_base = trim(urldecode($uri_base), '/');
        $this->uri = explode('/', $uri_base);

        $this->method = $params['REQUEST_METHOD'];

        parse_str($params['QUERY_STRING'], $this->queryString);
    }
}
