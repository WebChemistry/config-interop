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
		$toUnpack = null;
		$keep = $arguments['keep'] ?? [];

		foreach ($arguments as $i => $argument) {
			if (is_numeric($i)) {
				$toUnpack[] = $argument;
			}
		}

		foreach ($builder->getOriginal(false) as $keyToUnpack => $value) {
			if (is_array($value) && ($toUnpack === null || in_array($keyToUnpack, $toUnpack, true))) {
				foreach ($value as $key => $item) {
					$builder->setOriginalValue($key, $item);
				}

				if (!in_array($keyToUnpack, $keep, true)) {
					$builder->removeFromOriginal($keyToUnpack);
				}
			}
		}
	}

}
