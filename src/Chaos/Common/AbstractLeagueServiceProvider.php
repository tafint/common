<?php namespace Chaos\Common;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class AbstractLeagueServiceProvider
 * @author ntd1712
 */
abstract class AbstractLeagueServiceProvider extends AbstractServiceProvider
{
    /** {@inheritdoc} */
    public function register()
    {
        if (empty($this->provides))
        {
            return;
        }

        foreach ($this->provides as $v)
        {
            if (false !== strpos($v, '\\'))
            {
                $this->container->add($v);

                if (false === stripos($v, '\\events\\'))
                {
                    $this->container->add(shorten($v), $v);
                }
            }
        }
    }
}