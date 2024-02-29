<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Directive\Setup;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Directive\Setup\SetupFunction;
use WebChemistry\ConfigInterop\Structure;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValueBuilder;
use WebChemistry\ConfigInterop\StructureValues;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class CommentSetupFunction implements SetupFunction, Visitor
{

	public function register(VisitorRegistry $registry): void
	{
		$registry->addEnter(function (ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void {
			if ($value->getOptions()->has('comment')) {
				$builder->comment($value->getOptions()->getString('comment'));
			}
		});
	}

	public function getName(): string
	{
		return 'comment';
	}

	public function invokeSetupFunction(array $arguments, Structure $structure, StructureValueBuilder $builder, GeneratorContext $context): void
	{
		$builder->setOption('comment', $arguments[0] ?? null);
	}

}
