<?php namespace Chaos\Common;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class AbstractLaravelController
 * @author ntd1712
 */
abstract class AbstractLaravelController extends Controller
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait, Traits\ServiceAwareTrait,
        BaseControllerTrait, DispatchesCommands, ValidatesRequests;

    /**
     * Constructor
     *
     * @param   array|string $config The path to the config file
     * @param   array|\ArrayAccess $container
     */
    public function __construct($config = [], $container = [])
    {
        $this->setConfig($config)
             ->setContainer($container)
             ->getContainer()->share(DOCTRINE_ENTITY_MANAGER, app(DOCTRINE_ENTITY_MANAGER));
    }

    /** {@inheritdoc} @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        $request = $this->getRouter()->getCurrentRequest();

        return isset($key) ? $request->get($key, $default, $deep) : (
        false === $default ? $request->all() : $request->all() + [
            'ModifiedAt' => 'now',
            'ModifiedBy' => \Session::get('loggedName'),
            'IsDeleted' => false,
            'ApplicationKey' => $this->getConfig('app.key')
        ]);
    }

    /**
     * Get the <tt>user</tt> instance
     *
     * @param   string $token The JWT token; defaults to JWTAuth::getToken()
     * @return  IBaseEntity
     * @throws  JWTException
     */
    protected function getUser($token = null)
    {
        if (null === ($user = \Session::get('loggedUser')))
        {
            $payload = \JWTAuth::getPayload($token ?: \JWTAuth::getToken());
            $user = $this->getService('User')->getRepository()->find($payload['sub']);

            if (null === $user)
            {
                throw new JWTException('User not found', 404);
            }
        }

        return $user;
    }
}