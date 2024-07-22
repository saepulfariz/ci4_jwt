<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

Class Options implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
		 header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        // Handle preflight requests
        if ($request->getMethod() === 'options') {
            header('HTTP/1.1 200 OK');
            exit;
        }
		header("Access-Control-Allow-Origin: http://localhost:5173");
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: *");
		$allowed_domains = [
			'http://localhost',
			'http://localhost:5173',
			'http://localhost:8060',
		];
		// header("Access-Control-Allow-Origin: http://localhost");
		if (isset($_SERVER)) {
			$http_origin = (isset($_SERVER['HTTP_ORIGIN'])) ? $_SERVER['HTTP_ORIGIN'] : '';
			if (in_array($http_origin, $allowed_domains)) {
				//header("Access-Control-Allow-Origin: $http_origin");
			}
		}
        //header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        //header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
        die();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
      // Do something here
    }
}