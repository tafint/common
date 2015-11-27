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
        $fromClause = $this->getQuery()->getAST()->fromClause;
        $from = $fromClause->identificationVariableDeclarations;

        if (1 === count($from))
        {
            /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
            $metadata = $this->getQueryComponent($from[0]->rangeVariableDeclaration->aliasIdentificationVariable)['metadata'];

            if (isset($metadata->fieldMappings['ApplicationKey']))
            {
                $parts = explode(' ', $fromClause->dispatch($this));

                return ($sql ? $sql . ' AND ' : ' WHERE ') . strtr(":table.:column = ':value'", [
                    ':table' => end($parts),
                    ':column' => 'application_key',
                    ':value' => $this->getQuery()->getHint('config')->get('app.key')
                ]);
            }
        }

        return $sql;
    }
}