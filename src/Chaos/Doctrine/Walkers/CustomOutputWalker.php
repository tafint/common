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

        if (null !== ($config = $this->getQuery()->getHint('config')) && $config->get('multitenant.enabled'))
        {
            $fromClause = $this->getQuery()->getAST()->fromClause;
            $from = $fromClause->identificationVariableDeclarations;

            if (1 === count($from))
            {   /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
                $metadata = $this->getQueryComponent(
                    $from[0]->rangeVariableDeclaration->aliasIdentificationVariable)['metadata'];
                $keymap = $config->get('multitenant.keymap');

                if (isset($metadata->fieldMappings[$keymap]))
                {
                    $parts = explode(' ', $fromClause->dispatch($this));

                    return ($sql ? $sql . ' AND ' : ' WHERE ') . strtr(":table.:column = ':value'", [
                        ':table' => end($parts),
                        ':column' => $metadata->fieldMappings[$keymap]['columnName'],
                        ':value' => $config->get('app.key')
                    ]);
                }
            }
        }

        return $sql;
    }
}