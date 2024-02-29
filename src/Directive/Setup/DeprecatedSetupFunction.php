<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class DeprecatedSetupFunction implements SetupFunction
{

	public function getName(): string
	{
		return 'deprecated';
	}

	public function invokeSetupFunction(
		array $arguments,
		callable $parse,
		Structure $structure,
		StructureValueBuilder $builder,
	): void
	{
		$builder->setInheritedValueOption('deprecated', $arguments[0] ?? '');
	}

}
