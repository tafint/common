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
                $this->getContainer()->add($v);

                if (false === stripos($v, '\\events\\'))
                {
                    $this->getContainer()->add(shorten($v), $v);
                }
            }
        }
    }
}