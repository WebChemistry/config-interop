<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive;

use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

interface Directive
{

	public function getName(): string;

	/**
	 * @param mixed $value
	 */
	public function invokeDirective(mixed $value, Structure $structure, StructureValueBuilder $builder): void;

}
