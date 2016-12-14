<?php namespace Chaos\Common;

/**
 * Class AbstractCodeIgniterRestController
 * @author ntd1712
 */
abstract class AbstractCodeIgniterRestController extends AbstractCodeIgniterController
{
    /**
     * The default "index" action, you can override this in derived class
     *
     * @return  array
     * @example GET /lookup?filter=&sort=&start=&length=
     */
    public function index()
    {
        return $this->getService()->readAll($this->getFilterParams(), $this->getPagerParams());
    }

    /**
     * The default "create" action, you can override this in derived class
     *
     * @return  array
     * @throws  Exceptions\BadMethodCallException
     * @example GET /lookup/create
     */
    public function create()
    {
        throw new Exceptions\BadMethodCallException('Unknown method ' . __METHOD__);
    }

    /**
     * The default "store" action, you can override this in derived class
     *
     * @return  array
     * @example POST /lookup
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
     * @example GET /lookup/{lookup}
     */
    public function show($id)
    {
        return $this->getService()->read($id);
    }

    /**
     * The default "edit" action, you can override this in derived class
     *
     * @return  array
     * @throws  Exceptions\BadMethodCallException
     * @example GET /lookup/{lookup}/edit
     */
    public function edit()
    {
        throw new Exceptions\BadMethodCallException('Unknown method ' . __METHOD__);
    }

    /**
     * The default "update" action, you can override this in derived class
     *
     * @param   mixed $id
     * @return  array
     * @example PUT /lookup/{lookup}
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
     * @example DELETE /lookup/{lookup}
     */
    public function destroy($id)
    {
        return $this->getService()->delete($id);
    }
}