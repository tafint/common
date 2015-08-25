<?php namespace Chaos\Common;

/**
 * Class AbstractBaseObject
 * @author ntd1712
 */
abstract class AbstractBaseObject implements \JsonSerializable, IBaseObject
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait;

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /** {@inheritdoc} */
    public function fromJson($json, $assoc = false)
    {
        $message = 'JSON decoding failed: ';

        if (!CHAOS_USE_EXTERNAL_JSON && function_exists('json_decode'))
        {
            $value = @call_user_func_array('json_decode', func_get_args());

            switch (json_last_error())
            {
                case JSON_ERROR_NONE:
                    return $value;
                default:
                    $message .= json_last_error_msg();
            }
        }
        elseif (class_exists(ZEND_JSON_DECODER))
        {
            try
            {   // to make errors meaningful
                return forward_static_call_array([ZEND_JSON_DECODER, 'decode'], func_get_args());
            }
            catch (\RuntimeException $ex)
            {
                $message .= $ex->getMessage();
            }
        }
        else
        {
            $message .= 'Not supported yet';
        }

        throw new Exceptions\RuntimeException($message);
    }

    /** {@inheritdoc} */
    public function toJson()
    {
        $message = 'JSON encoding failed: ';

        if (!CHAOS_USE_EXTERNAL_JSON && function_exists('json_encode'))
        {
            $value = @call_user_func_array('json_encode', array_merge([$this], func_get_args()));

            switch (json_last_error())
            {
                case JSON_ERROR_NONE:
                    return $value;
                default:
                    $message .= json_last_error_msg();
            }
        }
        elseif (class_exists(ZEND_JSON_ENCODER))
        {
            return forward_static_call_array([ZEND_JSON_ENCODER, 'encode'], array_merge([$this], func_get_args()));
        }
        else
        {
            $message .= 'Not supported yet';
        }

        throw new Exceptions\RuntimeException($message);
    }

    /** {@inheritdoc} */
    abstract public function toArray();

    /** @return string */
    public function __toString()
    {
        return str_replace('Entity', '', shorten(get_class($this)));
    }
}