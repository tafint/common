<?php namespace Chaos\Common\Exceptions;

/**
 * Class JWTException
 * @author ntd1712
 */
class JWTException extends \Exception implements IException
{
    protected $code = 500;
}