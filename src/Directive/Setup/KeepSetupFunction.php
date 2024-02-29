<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class KeepSetupFunction implements SetupFunction
{

	public function getName(): string
	{
		return 'keep';
	}

	public function invokeSetupFunction(
		array $arguments,
		callable $parse,
		Structure $structure,
		StructureValueBuilder $builder,
	): void
	{
		foreach ($builder->getOriginal(false) as $key => $_) {
			if (!in_array($key, $arguments, true)) {
				$builder->removeFromOriginal($key);
			}
		}
	}

}
