<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

use LogicException;
use Nette\Neon\Entity;
use Nette\Schema\Expect;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Directive\Directive;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Exception\SkipProcessionException;
use WebChemistry\ConfigInterop\Function\ConfigFunctions;
use WebChemistry\ConfigInterop\Schema\SchemaProcessor;

final class Structure
{

	/** @var Directive[] */
	private array $directives = [];

	private GeneratorContext $context;

	/**
	 * @param mixed[] $structure
	 * @param Directive[] $directives
	 */
	public function __construct(
		private array $structure,
		array $directives,
		private ConfigFunctions $functions,
	)
	{
		foreach ($directives as $directive) {
			$this->directives[$directive->getName()] = $directive;
		}

		$this->context = new GeneratorContext([]);
	}

	public function withContext(GeneratorContext $context): self
	{
		$new = clone $this;
		$new->context = $context;

		return $new;
	}

	/**
	 * @return array{language: string, file: string, context: mixed[]}[]
	 */
	public function getOutput(): array
	{
		// @phpstan-ignore-next-line
		return SchemaProcessor::process($this->structure['output'] ?? [], Expect::arrayOf(Expect::structure([
			'language' => Expect::string()->required(),
			'file' => Expect::string()->required(),
			'context' => Expect::array()->default([]),
		])));
	}

	public function getVariables(): StructureValues
	{
		$variables = SchemaProcessor::process(
			$this->structure['variables'] ?? [],
			Expect::arrayOf(Expect::mixed(), Expect::string()),
		);

		// @phpstan-ignore-next-line
		return $this->processValues($variables) ?? new StructureValues();
	}

	public function getContext(): GeneratorContext
	{
		return $this->context;
	}

	/**
	 * @param mixed[] $values
	 */
	private function processValues(array $values): ?StructureValues
	{
		$builder = new StructureValueBuilder($values, functions: $this->functions);

		foreach ($this->directives as $directive) {
			$key = $directive->getName();
			$original = $builder->getOriginal();

			if (array_key_exists($key, $original)) {
				try {
					$directive->invokeDirective($original[$key], $this, $builder);
				} catch (SkipProcessionException) {
					return null;
				}
			}

			$builder->removeFromOriginal($key);
		}

		foreach ($builder->getOriginal() as $key => $value) {
			if (is_array($value)) {
				$value = $this->processValues($value);

				if ($value !== null) {
					$builder->addValue($key, $value);
				}

			} else {
				if (!$value instanceof Entity && !is_scalar($value) && $value !== null) {
					throw new LogicException(sprintf('Value must be scalar or array or function call, %s given', get_debug_type($value)));
				}

				$builder->addValue($key, $builder->createValue($value));
			}
		}

		return $builder->build();
	}

}
