<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class ChainSetupFunction implements SetupFunction
{

	public function getName(): string
	{
		return 'chain';
	}

	public function invokeSetupFunction(
		array $arguments,
		callable $parse,
		Structure $structure,
		StructureValueBuilder $builder,
	): void
	{
		foreach ($arguments as $argument) {
			$parse($argument);
		}
	}

}
