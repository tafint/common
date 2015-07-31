<?php namespace Chaos\Common;

/**
 * Class AbstractLaravelRestController
 * @author ntd1712
 */
abstract class AbstractLaravelRestController extends AbstractLaravelController
{
    /**
     * The default "index" action, you can override this in derived class
     *
     * @return  array
     * @link    GET /lookup?filter=&sort=&start=&length=
     */
    public function index()
    {
        return $this->getService()->readAll($this->getFilterParams(), $this->getPagerParams());
    }

    /**
     * The default "create" action, you can override this in derived class
     *
     * @return  array
     * @link    GET /lookup/create
     * @throws  Exceptions\BadMethodCallException
     */
    public function create()
    {
        throw new Exceptions\BadMethodCallException(sprintf('Unknown method "%s"', __METHOD__));
    }

    /**
     * The default "store" action, you can override this in derived class
     *
     * @return  array
     * @link    POST /lookup
     */
    public function store()
    {
        return $this->getService()->create($this->getRequest());
    }

    /**
     * The default "show" action, you can override this in derived class
     *
     * @param   mixed $id
     * @return  array
     * @link    GET /lookup/{lookup}
     */
    public function show($id)
    {
        return $this->getService()->read($id);
    }

    /**
     * The default "edit" action, you can override this in derived class
     *
     * @return  array
     * @link    GET /lookup/{lookup}/edit
     * @throws  Exceptions\BadMethodCallException
     */
    public function edit()
    {
        throw new Exceptions\BadMethodCallException(sprintf('Unknown method "%s"', __METHOD__));
    }

    /**
     * The default "update" action, you can override this in derived class
     *
     * @param   mixed $id
     * @return  array
     * @link    PUT /lookup/{lookup}
     */
    public function update($id)
    {
        return $this->getService()->update($this->getRequest(), $id);
    }

    /**
     * The default "destroy" action, you can override this in derived class
     *
     * @param   mixed $id
     * @return  array
     * @link    DELETE /lookup/{lookup}
     */
    public function destroy($id)
    {
        return $this->getService()->delete($id);
    }
}