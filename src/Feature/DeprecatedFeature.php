<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Feature;

use InvalidArgumentException;
use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Function\ConfigFunction;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValues;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class DeprecatedFeature implements ConfigFunction, Visitor
{

	public function getName(): string
	{
		return 'deprecated';
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function invokeConfigFunction(array $arguments): StructureValue
	{
		$value = $arguments[0] ?? throw new InvalidArgumentException('Missing argument');

		if (!is_scalar($value) && $value !== null) {
			throw new InvalidArgumentException('Value must be scalar or null');
		}

		return new StructureValue($value, [
			'deprecated' => $arguments[1] ?? '',
		]);
	}

	public function register(VisitorRegistry $registry): void
	{
		$registry->addEnter($this->generate(...), $registry::PriorityBeforeLanguageHigh);

//		$registry->addDynamic(function (VisitorRegistry $registry, GeneratorContext $context): void {
//			if ($context->getLanguageContext()->commentLocation === Location::Above) {
//				$registry->addEnter(function (ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void {
//					$this->generate($builder, $value, $context);
//
//					if ($value->getOptions()->has('deprecated')) {
//						$builder->newLine();
//					}
//				}, $registry::priorityBefore(2));
//			} else {
//				$registry->addEnter($this->generate(...), $registry::PriorityAfterLanguageHigh);
//			}
//		});
	}

	private function generate(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void
	{
		if (!$value->getOptions()->has('deprecated')) {
			return;
		}

		$builder->comment(implode(' ', array_filter(['@deprecated', $value->getOptions()->getString('deprecated')])));
	}

}
