<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Powermail: Static From in Mails',
    'description' => 'Use reply-to as default from address, set static value when configured',
    'category' => 'module',
    'version' => '3.0.2',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'createDirs' => '',
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'powermail' => '9.0.0-9.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Serfhos\\PowermailFromOverride\\' => 'Classes',
        ],
    ],
];

