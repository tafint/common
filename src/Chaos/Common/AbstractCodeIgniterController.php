<?php namespace Chaos\Common;

use Chaos\Doctrine\EntityManagerFactory;

if (defined('REST_Controller'))
{
    class Controller extends \REST_Controller {}
}
else
{
    class Controller extends \CI_Controller {}
}

/**
 * Class AbstractCodeIgniterController
 * @author ntd1712
 *
 * @property-read object $input
 * @property-read object $session
 */
abstract class AbstractCodeIgniterController extends Controller
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait, Traits\ServiceAwareTrait,
        BaseControllerTrait;

    /**
     * Constructor
     *
     * @param   array|string $config The path to the config file
     * @param   array|\ArrayAccess $container
     */
    public function __construct($config = [], $container = [])
    {
        parent::__construct();

        $this->setConfig($config)
             ->setContainer($container)
             ->getContainer()->share(DOCTRINE_ENTITY_MANAGER,
                $entityManager = (new EntityManagerFactory())->setConfig($this->getConfig())->getEntityManager());

        /** @var \Doctrine\ORM\EntityManager $entityManager
            @var \Doctrine\ORM\Configuration $configuration */
        $configuration = $entityManager->getConfiguration();
        $configuration->setDefaultQueryHint('config', $config = $this->getConfig());

        foreach ($config->get('orm.walkers') as $k => $v)
        {
            $configuration->setDefaultQueryHint($k, $v);
        }
    }

    /** {@inheritdoc} @override @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        return isset($key) ? $this->input->post_get($key) : (
        false === $default ? $this->input->post_get(null) : $this->input->post_get(null) + [
            'EditedAt' => 'now',
            'EditedBy' => $this->session->userdata('loggedName'),
            'IsDeleted' => false,
            'ApplicationKey' => $this->getConfig()->get('app.key')
        ]);
    }
}