<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Function;

use WebChemistry\ConfigInterop\StructureValue;

interface ConfigFunction
{

	public function getName(): string;

	/**
	 * @param mixed[] $arguments
	 */
	public function invokeConfigFunction(array $arguments): StructureValue;

}
