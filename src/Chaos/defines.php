<?php

// common: service, repository & entity
define('CHAOS_MAX_QUERY', 10);
define('CHAOS_MAX_RECURSION_DEPTH', 14);
define('CHAOS_USE_INTERNAL_JSON', true);
define('CHAOS_ANNOTATION_IGNORE', '[Ignore]');
define('CHAOS_ANNOTATION_IGNORE_TYPE_JUGGLING', '[IgnoreTypeJuggling]');
define('CHAOS_ANNOTATION_IGNORE_RULES', '[IgnoreRules]');
define('CHAOS_ANNOTATION_IGNORE_DATA', '[IgnoreData]');

// common: regular expressions
define('CHAOS_MATCH_DATE', '#^[0-9]{1,4}[-/.][0-9]{1,2}[-/.][0-9]{1,4}$#');
define('CHAOS_MATCH_ASC_DESC', '#([^\s]+)\s*(asc|desc)?(.*)#i');
define('CHAOS_MATCH_COLUMN_DEFINITION', '#column\s*\([^)]*columndefinition\s*=\s*["\']\s*([^\s\(]+)(?:\([^\)]+\))?\s+[^"\']+["\'][^)]*\)#i');
define('CHAOS_MATCH_COLUMN_TYPE', '#column\s*\([^)]*type\s*=\s*["\']\s*([^"\']+)\s*["\'][^)]*\)#i');
define('CHAOS_MATCH_ONE_MANY', '#((?:one|many)to(?:one|many))\s*\([^)]*targetentity\s*=\s*["\']\s*([^"\']+)["\'][^)]*\)#i');
define('CHAOS_MATCH_RULE', '#\[\s*(.+)\s*\]#');
define('CHAOS_MATCH_RULE_ITEM', '#^\[(\w+)\s*(\([^\)]+\))?\]$#');
define('CHAOS_MATCH_VAR', '#@var\s+\\\?([^\s\*\(]+)\s*(?:\(\s*\\\?([^\s\*\)]+)\s*\))?#i');

define('CHAOS_REPLACE_COMMA_SEPARATOR', '#\s*,\s*#');
define('CHAOS_REPLACE_SPACE_SEPARATOR', '#\s+#');

// common: namespaces
define('CHAOS_JSON_DECODER', 'json_decode'); // Zend\Json\Decoder, Symfony\Component\Serializer\Encoder\JsonDecode, yii\helpers\Json
define('CHAOS_JSON_ENCODER', 'json_encode'); // Zend\Json\Encoder, Symfony\Component\Serializer\Encoder\JsonEncoder
define('CHAOS_READ_EVENT_ARGS', 'Chaos\Common\Events\ReadEventArgs');
define('DOCTRINE_ARRAY_COLLECTION', 'Doctrine\Common\Collections\ArrayCollection');
define('DOCTRINE_PERSISTENT_COLLECTION', 'Doctrine\ORM\PersistentCollection');
define('DOCTRINE_DRIVER_MANAGER', 'Doctrine\DBAL\DriverManager');
define('DOCTRINE_ENTITY_MANAGER', 'Doctrine\ORM\EntityManager');
define('DOCTRINE_PROXY', 'Doctrine\ORM\Proxy\Proxy');
define('ZEND_STATIC_FILTER', 'Zend\Filter\StaticFilter');
define('ZEND_STATIC_VALIDATOR', 'Zend\Validator\StaticValidator');