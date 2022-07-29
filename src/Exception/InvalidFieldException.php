<?php
namespace ProductList\Exception;

class InvalidFieldException extends \Exception
{
	public function __construct($field, $code = 0, Throwable $previous = null)
	{
		http_response_code(400);
		parent::__construct("The field '$field' is invalid.", $code, $previous);
	}
}
