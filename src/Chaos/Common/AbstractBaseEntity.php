<?php namespace Chaos\Common;

/**
 * Class AbstractBaseEntity
 * @author ntd1712
 *
 * @link http://ormcheatsheet.com
 */
abstract class AbstractBaseEntity extends AbstractBaseObjectItem implements IBaseEntity
{
    /** @var array */
    private static $identifier = [];
    /** @var array */
    private static $rules = [];

    /** {@inheritdoc} */
    public function __destruct()
    {
        self::$identifier = self::$rules = [];
    }

    /** {@inheritdoc} */
    public function addRule($property, $rule)
    {
        $name = $property->name;

        if (!array_key_exists($name, self::$rules))
        {
            self::$rules[$name] = ['property' => $property, 'rules' => [$rule]];
        }
        elseif (!in_array($rule, self::$rules[$name]['rules']))
        {
            self::$rules[$name]['rules'][] = $rule;
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return self::$identifier;
    }

    /** {@inheritdoc} */
    public function setIdentifier(array $identifier)
    {
        self::$identifier = $identifier;
        return $this;
    }

    /** {@inheritdoc} */
    public function validate()
    {
        if (empty(self::$rules))
        {
            return false; // no rules; defaults to FALSE
        }

        // initialize filter & validator
        $filters = ['php' => filter_list(), 'validated' => [],
            'filter' => [], 'filter_plugin' => null, 'validator' => [], 'validator_plugin' => null];

        if (class_exists(ZEND_STATIC_VALIDATOR))
        {
            $filters['validator_plugin'] = forward_static_call([ZEND_STATIC_VALIDATOR, 'getPluginManager']);
            $filters['validator'] = $filters['validator_plugin']->getRegisteredServices()['invokableClasses'];
        }

        if (class_exists(ZEND_STATIC_FILTER))
        {
            $filters['filter_plugin'] = forward_static_call([ZEND_STATIC_FILTER, 'getPluginManager']);
            $filters['filter'] = $filters['filter_plugin']->getRegisteredServices()['invokableClasses'];
        }

        foreach (self::$rules as $k => $v)
        {
            $value = $v['property']->getValue($this);
            $newValue = $value;
            $hasValue = !is_empty($value);

            foreach ($v['rules'] as $rule)
            {   // e.g. [full_special_chars('flags' => FILTER_FLAG_NO_ENCODE_QUOTES)]
                //      [HtmlEntities('quotestyle' => ENT_QUOTES, 'encoding' => 'UTF-8', 'doublequote' => true)]
                //      [StringLength('max' => 255, 'message' => 'Hi, ntd1712')]
                preg_match(CHAOS_MATCH_RULE_ITEM, $rule, $matches);

                if (!isset($matches[1])) // e.g. full_special_chars, HtmlEntities, StringLength
                {
                    continue;
                }

                $rule = strtolower($matches[1]);
                $options = [];

                if (!empty($matches[2])) // e.g. ('max' => 255, 'message' => 'Hi, ntd1712')
                {
                    $result = @eval('return array' . $matches[2] . ';');

                    if (null === error_get_last() && is_array($result))
                    {
                        $options = $result;

                        if (isset($options['message']))
                        {
                            $options['message'] = str_replace('{property}', $k, $options['message']);
                        }
                    }
                }

                // validator
                if ('notempty' === $rule || $hasValue)
                {
                    if (in_array($rule, $filters['validator']))
                    {   /** @var \Zend\Validator\AbstractValidator $validator */
                        $validator = $filters['validator_plugin']->get($rule, $options);
                        $filters['validated'][$rule] = true;

                        if (!$validator->isValid($value))
                        {
                            return $validator->getMessages();
                        }
                    }
                    elseif (in_array($rule, $filters['php']))
                    {
                        $result = filter_var($value, filter_id($rule), $options);
                        $filters['validated'][$rule] = true;

                        if (false === $result)
                        {
                            return [sprintf('Value of "%s" is not valid for "%s"',
                                32 < strlen($value) ? substr($value, 0, 20) . '...' : $value, $k)];
                        }
                    }
                }

                // filter
                if (!isset($filters['validated'][$rule]) && $hasValue)
                {
                    if (in_array($rule, $filters['filter']))
                    {   /** @var \Zend\Filter\AbstractFilter $filter */
                        $filter = $filters['filter_plugin']->get($rule, $options);
                        $newValue = $filter->filter($value);
                    }
                    elseif (in_array($rule, $filters['php']))
                    {
                        $result = filter_var($value, filter_id($rule), $options);

                        if (false !== $result)
                        {
                            $newValue = $result;
                        }
                    }

                    // set new property value (if any)
                    if ($value != $newValue)
                    {
                        $v['property']->setValue($this, $newValue);
                    }
                }
            }
        }

        return false;
    }
}