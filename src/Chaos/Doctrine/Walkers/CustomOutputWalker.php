<?php namespace Chaos\Doctrine\Walkers;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Class CustomOutputWalker
 * @author ntd1712
 */
class CustomOutputWalker extends SqlWalker
{
    /** {@inheritdoc} */
    public function walkWhereClause($whereClause)
    {
        $sql = parent::walkWhereClause($whereClause);

        return ($sql ? '(' . $sql . ') AND ' : ' WHERE ') . strtr("application_key = ':app_key'", [
            ':app_key' => $this->getQuery()->getHint('config')->get('app.key')
        ]);
    }
}