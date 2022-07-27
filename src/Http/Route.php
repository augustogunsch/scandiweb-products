<?php
namespace ProductList\Http;

class Route
{
    private $method;
    private $uri;
    private $view;

    public function __construct(string $method, string $uri, array $view)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->view = $view;
    }

    public function matches(Request $request)
    {
        return $this->method === $request->getMethod() && $this->uri === $request->getUri();
    }

    public function execute(Request $request)
    {
        call_user_func($this->view, $request);
    }
}
