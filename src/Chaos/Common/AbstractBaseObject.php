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
    public function fromJson($json, $assoc = true, $options = 0)
    {
        $message = 'JSON decoding failed: ';

        if (function_exists('json_decode') && !CHAOS_USE_EXTERNAL_JSON)
        {
            $decodedValue = @json_decode($json, $assoc, 512, $options);

            switch (json_last_error())
            {
                case JSON_ERROR_NONE:
                    return $decodedValue;
                default:
                    $message .= json_last_error_msg();
            }
        }
        elseif (class_exists(ZEND_JSON_DECODER))
        {
            try
            {   // to make errors meaningful
                return forward_static_call([ZEND_JSON_DECODER, 'decode'], $json, (int)$assoc);
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
    public function toJson($options = 15)
    {
        $message = 'JSON encoding failed: ';

        if (function_exists('json_encode') && !CHAOS_USE_EXTERNAL_JSON)
        {
            if (defined('JSON_PRETTY_PRINT'))
            {
                $options |= JSON_PRETTY_PRINT;
            }

            $encodedValue = @json_encode($this, $options);

            switch (json_last_error())
            {
                case JSON_ERROR_NONE:
                    return $encodedValue;
                default:
                    $message .= json_last_error_msg();
            }
        }
        elseif (class_exists(ZEND_JSON_ENCODER))
        {
            return forward_static_call([ZEND_JSON_ENCODER, 'encode'], $this, true,
                array_merge(['silenceCyclicalExceptions' => true], (array)$options));
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