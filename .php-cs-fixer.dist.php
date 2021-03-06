<?php

if (!file_exists(__DIR__.'/src')) {
    exit(0);
}

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src'])
;


$config = new PhpCsFixer\Config();
return $config->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'protected_to_private' => false,
        'semicolon_after_instruction' => false,
    ))
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setFinder($finder)
;
