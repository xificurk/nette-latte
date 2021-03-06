<?php

declare(strict_types=1);

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class LogPolicy implements Latte\Policy
{
	public $log = [];


	public function isMacroAllowed(string $macro): bool
	{
		return true;
	}


	public function isFilterAllowed(string $filter): bool
	{
		$this->log['filters'][] = $filter;
		return true;
	}


	public function isFunctionAllowed(string $function): bool
	{
		return true;
	}


	public function isMethodAllowed(string $class, string $method): bool
	{
		return true;
	}


	public function isPropertyAllowed(string $class, string $property): bool
	{
		return true;
	}
}


class MyClass
{
	public static function foo()
	{
	}
}


$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);
$latte->setTempDirectory(getTempDir());

$policy = new LogPolicy;
$latte->setPolicy($policy);
$latte->setSandboxMode();

$template = <<<'EOD'
{var $var = ("xxx"|upper|truncate:10)}
{$var|lower|truncate:20}

EOD;

$latte->compile($template);
Assert::same(
	[
		'filters' => ['upper', 'truncate', 'lower', 'truncate'],
	],
	$policy->log
);


$latte->warmupCache($template);
$policy->log = [];
$latte->renderToString($template);
Assert::same(
	[],
	$policy->log
);
