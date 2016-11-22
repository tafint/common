<?php namespace Chaos\Common;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

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
             ->getContainer()->share(DOCTRINE_ENTITY_MANAGER, $entityManager = app(DOCTRINE_ENTITY_MANAGER));

        /** @var \Doctrine\ORM\Configuration $configuration */
        $configuration = $entityManager->getConfiguration();
        $configuration->setDefaultQueryHint('config', $this->getConfig());

        foreach ($this->getConfig()->get('orm.walkers') as $k => $v)
        {
            $configuration->setDefaultQueryHint($k, $v);
        }
    }

    /** {@inheritdoc} @override @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        $request = $this->getRouter()->getCurrentRequest();

        return isset($key) ? $request->get($key, $default, $deep) : (
        false === $default ? $request->all() : $request->all() + [
            'EditedAt' => 'now',
            'EditedBy' => \Session::get('loggedName'),
            'IsDeleted' => false,
            'ApplicationKey' => $this->getConfig()->get('app.key')
        ]);
    }
}