<?php namespace Chaos\Common;

use Ramsey\Uuid\Uuid,
    Chaos\Doctrine\EntityManagerFactory;

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
                $entityManager = EntityManagerFactory::create()->setConfig($config = $this->getConfig())->getEntityManager());

        /** @var \Doctrine\ORM\EntityManager $entityManager
            @var \Doctrine\ORM\Configuration $configuration */
        $configuration = $entityManager->getConfiguration();
        $configuration->setDefaultQueryHint('config', $config);

        foreach ($config->get('orm.walkers') as $k => $v)
        {
            $configuration->setDefaultQueryHint($k, $v);
        }
    }

    /** {@inheritdoc} @override @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        if (defined('REST_Controller'))
        {
            $request = [];

            foreach (['get', 'delete', 'post', 'put'] as $v)
            {
                if ('head' !== $v)
                {
                    $request += $this->{'_' . $v . '_args'};
                }
            }
        }
        else
        {
            $request = (array)@json_decode($this->input->raw_input_stream, true) + $this->input->post_get(null);
        }

        return isset($key) ? (@$request[$key] ?: $default) : (
        false === $default ? $request : $request + [
            'EditedAt' => 'now',
            'EditedBy' => $this->session->userdata('loggedName'),
            'IsDeleted' => false,
            'Uuid' => Uuid::uuid4()->toString(),
            'ApplicationKey' => $this->getConfig()->get('app.key')
        ]);
    }
}