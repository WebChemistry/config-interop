<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use LogicException;
use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Exception\SkipProcessionException;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class SkipIfSetupFunction implements SetupFunction
{

	private ExpressionLanguage $language;

	public function __construct()
	{
		$this->language = new ExpressionLanguage();
	}

	public function getName(): string
	{
		return 'skipIf';
	}

	public function invokeSetupFunction(array $arguments, Structure $structure, StructureValueBuilder $builder, GeneratorContext $context): void
	{
		$expression = $arguments[0] ?? null;

		if (!is_string($expression)) {
			throw new LogicException('Expression is required');
		}

		$variables = [
			'context' => $context->all(),
		];

		if ($this->language->evaluate($expression, $variables)) {
			throw new SkipProcessionException();
		}
	}

}
