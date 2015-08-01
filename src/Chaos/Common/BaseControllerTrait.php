<?php namespace Chaos\Common;

/**
 * Trait BaseControllerTrait
 * @author ntd1712
 */
trait BaseControllerTrait
{
    /**
     * Either get a parameter value or all of the input and files
     *
     * @param   string $key Parameter key
     * @param   mixed $default Default value
     * @return  array|mixed
     */
    abstract protected function getRequest($key = null, $default = null);
    /**
     * Get the <tt>service</tt> instance
     *
     * @param   string $name Service name; defaults to get_called_class()
     * @return  IBaseService
     */
    abstract protected function getService($name = null);

    /**
     * Get the filter parameters
     *
     * Support the following parameters:
     * ?filter=[
     *  {"predicate":"between|notBetween","identifier":"ModifiedAt","minValue":"9/29/2014","maxValue":"10/29/2014","combine":"and|or","nesting":"nest|unnest"},
     *  {"predicate":"equalTo|notEqualTo|lessThan|greaterThan|lessThanOrEqualTo|greaterThanOrEqualTo",
     *   "left":"Name","right":"ntd1712","leftType":"identifier","rightType":"value","combine":"and|or","nesting":"nest|unnest"},
     *  {"predicate":"expression","expression":"DATEDIFF(?,?)>?","parameters":["AddedAt","ModifiedAt",15],"combine":"and|or","nesting":"nest|unnest"}
     *  {"predicate":"in|notIn","identifier":"Name","valueSet":["ntd1712","dzung",3],"combine":"and|or","nesting":"nest|unnest"},
     *  {"predicate":"isNull|isNotNull","identifier":"Name","combine":"and|or","nesting":"nest|unnest"},
     *  {"predicate":"like|notLike","identifier":"Name","like|notLike":"ntd1712","combine":"and|or","nesting":"nest|unnest"}
     *  {"predicate":"literal","literal":"Name='ntd1712'","combine":"and|or","nesting":"nest|unnest"}
     * ]
     * &filter=ntd1712
     * &sort=[
     *  {"property":"Id","direction":"desc"},
     *  {"property":"Name","direction":"asc"}
     * ]
     * &sort=name&direction=DESC
     *
     * Support the following declarations: $binds = [
     *  'where' => 'Id = 1 OR Name = "ntd1712"',
     *  'where' => ['Id' => 1, 'Name' => 'ntd1712'] // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => ['Id' => 1, 'Name = "ntd1712"']  // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => new \Zend\Db\Sql\Predicate\PredicateSet
     * ]
     *
     * @param   array $binds A bind variable array
     * @param   string $key Request key
     * @return  array
     */
    protected function getFilterParams(array $binds = [], $key = 'filter')
    {
        $filter = $this->getRequest($key);

        if (!is_empty($filter))
        {
            if (is_string($filter))
            {
                $filter = trim(urldecode($filter));

                if (false !== ($decodedValue = is_json($filter, true)))
                {
                    $filter = $decodedValue;
                }
            }

            /** @see BaseServiceTrait::prepareFilterParams */
            $filterSet = $this->getService()->prepareFilterParams($filter);

            if (0 !== count($filterSet))
            {
                if (isset($binds['where']))
                {
                    $filterSet->addPredicates($binds['where']);
                }

                $binds['where'] = $filterSet;
            }
        }

        return $this->getOrderParams($binds);
    }

    /**
     * Get the sort order parameters
     *
     * Support the following parameters:
     * ?sort=[
     *  {"property":"Id","direction":"desc"},
     *  {"property":"Name","direction":"asc"}
     * ]
     * &sort=name&direction=desc
     *
     * Support the following declarations: $binds = [
     *  'order' => 'Id DESC, Name ASC',
     *  'order' => ['Id', 'Name ASC'],
     *  'order' => ['Id' => 'DESC', 'Name' => 'ASC']
     * ]
     *
     * @param   array $binds A bind variable array
     * @param   string $key Request key
     * @return  array
     */
    protected function getOrderParams(array $binds = [], $key = 'sort')
    {
        $order = $this->getRequest($key);

        if (!is_empty($order))
        {
            if (is_string($order))
            {
                $order = trim(urldecode($order));

                if (false !== ($decodedValue = is_json($order, true)))
                {
                    $order = (array)$decodedValue;
                }
                else
                {
                    $order = [['property' => $order, 'direction' => $this->getRequest('direction')]];
                }
            }

            /** @see BaseServiceTrait::prepareOrderParams */
            $orderSet = $this->getService()->prepareOrderParams($order);

            if (!empty($orderSet))
            {
                if (empty($binds['order']))
                {
                    $binds['order'] = [];
                }

                foreach ($orderSet as $k => $v)
                {
                    if (is_string($binds['order']))
                    {
                        $binds['order'] .= ', ' . $k . ' ' . $v;
                    }
                    elseif (is_array($binds['order']))
                    {
                        $binds['order'][$k] = $v;
                    }
                }
            }
        }

        return $binds;
    }

    /**
     * Get the pager parameters
     *
     * Support the following parameters:
     *  ?page=1&length=10
     *  ?start=0&length=10
     *
     * Support the following declarations: $binds = [
     *  'CurrentPageStart' => 0,
     *  'CurrentPageNumber' => 1,
     *  'ItemCountPerPage' => 10
     * ]
     *
     * @param   array $binds A bind variable array
     * @param   array $keys Request keys; defaults to ['page', 'length']
     * @return  array
     */
    protected function getPagerParams(array $binds = [], array $keys = ['page', 'length'])
    {
        /** @see BaseServiceTrait::preparePagerParams */
        return $this->getService()->preparePagerParams($binds + [
            'CurrentPageStart' => $this->getRequest('start'),
            'CurrentPageNumber' => $this->getRequest(@$keys[0]),
            'ItemCountPerPage' => $this->getRequest(@$keys[1])
        ]);
    }
}