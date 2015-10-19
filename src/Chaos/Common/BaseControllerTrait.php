<?php namespace Chaos\Common;

/**
 * Trait BaseControllerTrait
 * @author ntd1712
 *
 * @method AbstractBaseService|IBaseService getService(string $name = null, bool $cache = true)
 */
trait BaseControllerTrait
{
    /**
     * Either get a parameter value or all of the input and files
     *
     * @param   string $key
     * @param   mixed $default
     * @return  array|mixed
     */
    abstract protected function getRequest($key = null, $default = null);

    /**
     * Get the filter parameters
     * e.g.
     * ?filter=[
     *  {"predicate":"equalTo","left":"Id","right":"1","leftType":"identifier","rightType":"value","combine":"AND","nesting":"nest"},
     *  {"predicate":"equalTo","left":"Id","right":"2","leftType":"identifier","rightType":"value","combine":"OR"},
     *  {"predicate":"like","identifier":"Name","like":"demo","combine":"and","nesting":"unnest"}
     * ]
     * // equivalent to
     * $predicate = new \Zend\Db\Sql\Predicate\Predicate;
     * $predicate->nest()
     *  ->equalTo('Id', 1)
     *  ->or
     *  ->equalTo('Id', 2)
     *  ->unnest()
     *  ->and
     *  ->like('Name', '%demo%');
     *
     * Support the following parameters:
     * ?filter=[
     *  {"predicate":"between|notBetween","identifier":"ModifiedAt","minValue":"9/29/2014","maxValue":"10/29/2014","combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"equalTo|notEqualTo|lessThan|greaterThan|lessThanOrEqualTo|greaterThanOrEqualTo",
     *   "left":"Name","right":"ntd1712","leftType":"identifier","rightType":"value","combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"expression","expression":"CONCAT(?0,?1) IS NOT NULL","parameters":["AddedAt","ModifiedAt"],"combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"in|notIn","identifier":"Name","valueSet":["ntd1712","dzung",3],"combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"isNull|isNotNull","identifier":"Name","combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"like|notLike","identifier":"Name","like|notLike":"ntd1712","combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"literal","literal":"IsDeleted=false","combine":"AND|OR","nesting":"nest|unnest"}
     * ]
     * &filter=ntd1712
     *
     * Support the following declarations: $binds = [
     *  'where' => 'Id = 1 OR Name = "ntd1712"',
     *  'where' => ['Id' => 1, 'Name' => 'ntd1712'] // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => ['Id' => 1, 'Name = "ntd1712"']  // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => new \Zend\Db\Sql\Predicate\Predicate
     * ]
     *
     * @param   array $binds A bind variable array
     * @param   string $key The request key; defaults to "filter"
     * @return  array
     */
    protected function getFilterParams(array $binds = [], $key = 'filter')
    {
        $filter = $this->getRequest($key);

        if (!is_blank($filter))
        {
            if (is_string($filter))
            {
                $filter = trim(rawurldecode($filter));

                if (false !== ($decodedValue = is_json($filter, true)))
                {
                    $filter = $decodedValue;
                }
            }

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
     *  {"property":"Id","direction":"desc","nulls":"first"},
     *  {"property":"Name","direction":"asc","nulls":"last"}
     * ]
     * &sort=name&direction=desc&nulls=first
     *
     * Support the following declarations: $binds = [
     *  'order' => 'Id DESC, Name',
     *  'order' => 'Id DESC NULLS FIRST, Name ASC NULLS LAST',
     *  'order' => ['Id DESC NULLS FIRST', 'Name ASC NULLS LAST'],
     *  'order' => ['Id' => 'DESC NULLS FIRST', 'Name' => 'ASC NULLS LAST']
     * ]
     *
     * @param   array $binds A bind variable array
     * @param   string $key The request key; defaults to "sort"
     * @return  array
     */
    protected function getOrderParams(array $binds = [], $key = 'sort')
    {
        $order = $this->getRequest($key);

        if (!is_blank($order))
        {
            if (is_string($order))
            {
                $order = trim(rawurldecode($order));

                if (false !== ($decodedValue = is_json($order, true)))
                {
                    $order = (array)$decodedValue;
                }
                else
                {
                    $order = [['property' => $order, 'direction' => $this->getRequest('direction'),
                        'nulls' => $this->getRequest('nulls')]];
                }
            }

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
     * @param   array $keys The request keys; defaults to ['page', 'length']
     * @return  array
     */
    protected function getPagerParams(array $binds = [], array $keys = ['page', 'length'])
    {
        return $this->getService()->preparePagerParams($binds + [
            'CurrentPageStart' => $this->getRequest('start'),
            'CurrentPageNumber' => $this->getRequest(@$keys[0]),
            'ItemCountPerPage' => $this->getRequest(@$keys[1])
        ]);
    }
}