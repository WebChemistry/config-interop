<?php declare(strict_types = 1);

use Tester\Assert;
use WebChemistry\ConfigInterop\Adapter\ScssAdapter;
use WebChemistry\ConfigInterop\Structure;

require __DIR__ . '/../bootstrap.php';

$adapter = new ScssAdapter();
Assert::same('', $adapter->generate(new Structure([
	'variables' => [
		'font' => [
			'family' => [
				'name' => 'Arial',
				'fullName' => 'Arial, sans-serif',
			],
		],
	],
])));
