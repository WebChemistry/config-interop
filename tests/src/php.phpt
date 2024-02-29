<?php declare(strict_types = 1);

use Tester\Assert;
use WebChemistry\ConfigInterop\Adapter\PhpAdapter;
use WebChemistry\ConfigInterop\Structure;

require __DIR__ . '/../bootstrap.php';

$adapter = new PhpAdapter();
Assert::same("<?php declare(strict_types = 1);

return [
	'font' => [
		'family' => [
			'name' => 'Arial',
			'fullName' => 'Arial, sans-serif',
		],
	],
];
", $adapter->generate(new Structure([
	'variables' => [
		'font' => [
			'family' => [
				'name' => 'Arial',
				'fullName' => 'Arial, sans-serif',
			],
		],
	],
])));

Assert::same("", $adapter->generate(new Structure([
	'variables' => [
		'font' => [
			'family' => [
				'name' => 'Arial',
				'fullName' => 'Arial, sans-serif',
			],
		],
	],
]), ['className' => 'Config']));
