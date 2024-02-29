<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive;

use LogicException;
use Nette\Neon\Entity;
use WebChemistry\ConfigInterop\Directive\Setup\ChainSetupFunction;
use WebChemistry\ConfigInterop\Directive\Setup\IfSetupFunction;
use WebChemistry\ConfigInterop\Directive\Setup\KeepSetupFunction;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Directive\Setup\SkipSetupFunction;
use WebChemistry\ConfigInterop\Directive\Setup\UnpackSetupFunction;
use WebChemistry\ConfigInterop\Generator;
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

	public static function registerCoreFunctions(Generator $generator): void
	{
		$generator->addFeature(new IfSetupFunction());
		$generator->addFeature(new SkipSetupFunction());
		$generator->addFeature(new ChainSetupFunction());
		$generator->addFeature(new KeepSetupFunction());
		$generator->addFeature(new UnpackSetupFunction());
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

			$this->parse($entity, $structure, $builder);
		}
	}

	private function parse(mixed $value, Structure $structure, StructureValueBuilder $builder): mixed
	{
		if (!$value instanceof Entity) {
			return $value;
		}

		if (!is_string($value->value)) {
			throw new LogicException(sprintf('Directive %s expects Entity with string name', $this->getName()));
		}

		$function = $this->functions[$value->value] ?? null;

		if (!$function) {
			throw new LogicException(sprintf('Function %s not found', $value->value));
		}

		return $function->invokeSetupFunction(
			$value->attributes,
			fn (mixed $value) => $this->parse($value, $structure, $builder),
			$structure,
			$builder,
		);
	}

}
