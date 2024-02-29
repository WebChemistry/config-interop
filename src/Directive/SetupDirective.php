<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive;

use LogicException;
use Nette\Neon\Entity;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class SetupDirective implements Directive
{

	/** @var array<string, SetupFunction> */
	private array $functions = [];

	/**
	 * @param SetupFunction[] $functions
	 */
	public function __construct(array $functions)
	{
		foreach ($functions as $function) {
			$this->functions[$function->getName()] = $function;
		}
	}

	public function getName(): string
	{
		return '_setup';
	}

	/**
	 * @param mixed $value
	 */
	public function invokeDirective(mixed $value, Structure $structure, StructureValueBuilder $builder): void
	{
		if (!is_array($value)) {
			throw new LogicException(sprintf('Directive %s expects array', $this->getName()));
		}

		foreach ($value as $entity) {
			if (!$entity instanceof Entity) {
				throw new LogicException(sprintf('Directive %s expects array of Entity', $this->getName()));
			}

			if (!is_string($entity->value)) {
				throw new LogicException(sprintf('Directive %s expects Entity with string name', $this->getName()));
			}

			$function = $this->functions[$entity->value] ?? null;

			if (!$function) {
				throw new LogicException(sprintf('Function %s not found', $entity->value));
			}

			$function->invokeSetupFunction($entity->attributes, $structure, $builder, $structure->getContext());
		}
	}

}
