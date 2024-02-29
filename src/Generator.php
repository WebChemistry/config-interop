<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

use LogicException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Directive\Directive;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Directive\SetupDirective;
use WebChemistry\ConfigInterop\Function\ConfigFunction;
use WebChemistry\ConfigInterop\Function\ConfigFunctions;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class Generator
{

	private VisitorRegistry $registry;

	/** @var ConfigFunction[] */
	private array $functions = [];

	/** @var Directive[] */
	private array $directives = [];

	/** @var SetupFunction[] */
	private array $setupFunctions = [];

	public function __construct()
	{
		$this->registry = new VisitorRegistry();
	}

	public function addFeature(ConfigFunction|Visitor|Directive|SetupFunction $feature): self
	{
		if ($feature instanceof ConfigFunction) {
			$this->functions[] = $feature;
		}

		if ($feature instanceof Visitor) {
			$feature->register($this->registry);
		}

		if ($feature instanceof Directive) {
			$this->directives[] = $feature;
		}

		if ($feature instanceof SetupFunction) {
			$this->setupFunctions[] = $feature;
		}

		return $this;
	}

	/**
	 * @param array<string, scalar|null> $parameters
	 */
	public function generate(string $configFile, array $parameters = []): void
	{
		$config = Neon::decode(FileSystem::read($configFile));

		if (!is_array($config)) {
			throw new LogicException('Invalid config file structure');
		}

		$structure = new Structure(
			$config,
			$parameters,
			array_merge([
				new SetupDirective($this->setupFunctions),
			], $this->directives),
			new ConfigFunctions($this->functions),
		);

		foreach ($structure->getOutput() as $id => $output) {
			$language = $output['language'];
			$file = $output['file'];

			if (!$this->registry->hasLanguage($language)) {
				throw new LogicException(sprintf('Language %s is not registered', $language));
			}

			$context = new GeneratorContext(array_merge($output['context'], [
				'language' => $language,
				'id' => $id,
			]));

			FileSystem::write($file, $this->generateString($structure, $context));

			echo sprintf('Generated config for %s [%s]: %s', $language, $id, realpath($file)) . PHP_EOL;
		}
	}

	private function generateString(Structure $structure, GeneratorContext $context): string
	{
		$structure = $structure->withContext($context);

		$builder = new ContentBuilder();

		$context = $this->registry->initialize($context);

		$registry = $this->registry->createByContext($context);

		$registry->before($builder, $context);

		$this->walk($registry, $builder, $structure->getVariables(), $context);

		$registry->after($builder, $context);

		return $builder->getContent();
	}

	private function walk(VisitorRegistry $registry, ContentBuilder $builder, StructureValues $values, GeneratorContext $context): void
	{
		foreach ($values->getValues() as $value) {
			if ($value instanceof StructureValues) {
				$registry->enter($builder, $value, $context);

				$this->walk($registry, $builder, $value, $context);

				$registry->leave($builder, $value, $context);
			} else {
				$registry->enter($builder, $value, $context);
				$registry->leave($builder, $value, $context);
			}
		}
	}

}
