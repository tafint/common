<?php // path to entities for Doctrine Metadata Driver

return glob(__DIR__ . '/*/Entities', GLOB_ONLYDIR);

/*return [
    realpath(__DIR__ . '/Account/Entities'),
    realpath(__DIR__ . '/System/Entities')
];*/