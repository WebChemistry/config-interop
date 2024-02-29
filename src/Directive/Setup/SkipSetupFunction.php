<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Exception\SkipProcessionException;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class SkipSetupFunction implements SetupFunction
{

	public function getName(): string
	{
		return 'skip';
	}

	public function invokeSetupFunction(array $arguments, callable $parse, Structure $structure, StructureValueBuilder $builder): void
	{
		throw new SkipProcessionException();
	}

}
