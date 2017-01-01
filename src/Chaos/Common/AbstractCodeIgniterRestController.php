<?php namespace Chaos\Common;

/**
 * Class AbstractCodeIgniterRestController
 * @author ntd1712
 *
 * @method void set_response($data = null, $http_code = null)
 * @method mixed get($key = null, $xss_clean = null)
 */
abstract class AbstractCodeIgniterRestController extends AbstractCodeIgniterController
{
    /**
     * The default "index" action, you can override this in derived class
     *
     * @example GET /lookup?filter=&sort=&start=&length=
     */
    public function index_get()
    {
        $data = $this->getService()->readAll($this->getFilterParams(), $this->getPagerParams());
        $this->set_response($data);
    }

    /**
     * The default "create" action, you can override this in derived class
     *
     * @throws  Exceptions\BadMethodCallException
     * @example GET /lookup/create
     */
    public function create_get()
    {
        throw new Exceptions\BadMethodCallException('Unknown method ' . __METHOD__);
    }

    /**
     * The default "store" action, you can override this in derived class
     *
     * @example POST /lookup
     */
    public function store_post()
    {
        $data = $this->getService()->create($this->getRequest());
        $this->set_response($data);
    }

    /**
     * The default "show" action, you can override this in derived class
     *
     * @example GET /lookup/{lookup}
     */
    public function show_get()
    {
        $data = $this->getService()->read($this->get('id'));
        $this->set_response($data);
    }

    /**
     * The default "edit" action, you can override this in derived class
     *
     * @throws  Exceptions\BadMethodCallException
     * @example GET /lookup/{lookup}/edit
     */
    public function edit_get()
    {
        throw new Exceptions\BadMethodCallException('Unknown method ' . __METHOD__);
    }

    /**
     * The default "update" action, you can override this in derived class
     *
     * @example PUT /lookup/{lookup}
     */
    public function update_put()
    {
        $data = $this->getService()->update($this->getRequest(), $this->get('id'));
        $this->set_response($data);
    }

    /**
     * The default "destroy" action, you can override this in derived class
     *
     * @example DELETE /lookup/{lookup}
     */
    public function destroy_delete()
    {
        $data = $this->getService()->delete($this->get('id'));
        $this->set_response($data);
    }
}