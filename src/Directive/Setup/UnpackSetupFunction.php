<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class UnpackSetupFunction implements SetupFunction
{

	public function getName(): string
	{
		return 'unpack';
	}

	public function invokeSetupFunction(
		array $arguments,
		callable $parse,
		Structure $structure,
		StructureValueBuilder $builder,
	): void
	{
		foreach ($builder->getOriginal(false) as $keyToRemove => $value) {
			if (is_array($value)) {
				foreach ($value as $key => $item) {
					$builder->setOriginalValue($key, $item);
				}

				$builder->removeFromOriginal($keyToRemove);
			}
		}
	}

}
