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
        $parts = explode(' ', $this->getQuery()->getAST()->fromClause->dispatch($this));

        return ($sql ? $sql . ' AND ' : ' WHERE ') . strtr(":table.:column = ':value'", [
            ':table' => end($parts),
            ':column' => 'application_key',
            ':value' => $this->getQuery()->getHint('config')->get('app.key')
        ]);
    }
}