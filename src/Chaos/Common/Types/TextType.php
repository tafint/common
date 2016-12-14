<?php namespace Chaos\Common\Types;

/**
 * Class TextType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\TextType
 */
class TextType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        return is_resource($value) ? stream_get_contents($value) : $value;
    }
}