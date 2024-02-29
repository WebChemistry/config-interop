<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Function;

use InvalidArgumentException;
use Nette\Neon\Entity;
use WebChemistry\ConfigInterop\StructureValue;

final class ConfigFunctions
{

	/** @var array<string, ConfigFunction> */
	private array $functions = [];

	/**
	 * @param ConfigFunction[] $functions
	 */
	public function __construct(array $functions)
	{
		foreach ($functions as $function) {
			$this->functions[$function->getName()] = $function;
		}
	}

	public function getFunction(string $name): ConfigFunction
	{
		return $this->functions[$name] ?? throw new InvalidArgumentException(sprintf('Function %s not found', $name));
	}

	public function callByEntity(Entity $entity): StructureValue
	{
		if (!is_string($entity->value)) {
			throw new InvalidArgumentException('Function name must be string');
		}

		foreach ($entity->attributes as $value => $attribute) {
			if ($attribute instanceof Entity) {
				$entity->attributes[$value] = $this->callByEntity($attribute);
			}
		}

		return $this->getFunction($entity->value)->invokeConfigFunction($entity->attributes);
	}

}
