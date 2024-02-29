<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use LogicException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use WebChemistry\ConfigInterop\Exception\SkipProcessionException;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValueBuilder;

final class IfSetupFunction implements SetupFunction
{

	private ExpressionLanguage $language;

	public function __construct()
	{
		$this->language = new ExpressionLanguage();
	}

	public function getName(): string
	{
		return 'if';
	}

	public function invokeSetupFunction(array $arguments, callable $parse, Structure $structure, StructureValueBuilder $builder): void
	{
		$expression = $arguments[0] ?? null;

		if (!is_string($expression)) {
			throw new LogicException('Expression is required');
		}

		$variables = [
			'context' => $structure->getContext()->all(),
		];

		if ($this->language->evaluate($expression, $variables)) {
			$parse($arguments[1] ?? null);
		} else {
			$parse($arguments[2] ?? null);
		}
	}

}
