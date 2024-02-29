<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

interface SetupFunction
{

	public function getName(): string;

	/**
	 * @param mixed[] $arguments
	 * @param callable(mixed): mixed $parse
	 * @return mixed|void
	 */
	public function invokeSetupFunction(array $arguments, callable $parse, Structure $structure, StructureValueBuilder $builder);

}
