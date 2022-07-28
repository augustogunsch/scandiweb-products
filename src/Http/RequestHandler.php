<?php
namespace ProductList\Http;

class RequestHandler
{
    private $request;
    private $routes;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function registerRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    public function handle()
    {
        foreach ($this->routes as $route) {
            if ($route->matches($this->request)) {
                try {
                    $route->execute($this->request);
                } catch (\Exception $e) {
                    http_response_code(500);
                    echo $e->getMessage();
                }
                return;
            }
        }

        http_response_code(404);
    }
}
