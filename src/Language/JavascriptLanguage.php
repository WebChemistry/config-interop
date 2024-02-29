<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Language;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Helper\CommentHelper;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValues;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class JavascriptLanguage implements Visitor
{

	public const Language = 'javascript';

	public function register(VisitorRegistry $registry): void
	{
		$registry->addLanguage(self::Language);

		$registry->addBefore($this->start(...), $registry::PriorityLanguage);
		$registry->addEnter($this->enter(...), $registry::PriorityLanguage);
		$registry->addLeave($this->leave(...), $registry::PriorityLanguage);
		$registry->addAfter($this->end(...), $registry::PriorityLanguage);
	}

	protected function start(ContentBuilder $builder, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder, 2);

		$builder->ln(sprintf('const %s = {', $context->getString('const')));
		$builder->increaseLevel();
	}

	protected function enter(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder);

		if ($value instanceof StructureValue) {
			$builder->append(sprintf('%s: %s,', $value->getCamelCaseFullKey(), var_export($value->value, true)));
		}
	}

	protected function leave(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		if ($value instanceof StructureValue) {
			$builder->newLine();
		}
	}

	protected function end(ContentBuilder $builder, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		$builder->decreaseLevel();
		$builder->ln('};');
	}

}
