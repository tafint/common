<?php namespace Chaos\Common;

/**
 * Class AbstractCodeIgniterController
 * @author ntd1712
 *
 * @property-read object $doctrine
 * @property-read object $input
 * @property-read object $session
 */
abstract class AbstractCodeIgniterController extends \CI_Controller
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
        $this->load->library(['doctrine', 'session']);

        $this->setConfig($config)
             ->setContainer($container)
             ->getContainer()->share(DOCTRINE_ENTITY_MANAGER, $entityManager = $this->doctrine->getEntityManager());

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