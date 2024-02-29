<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Language;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Helper\CommentHelper;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValues;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class ScssLanguage implements Visitor
{

	public const Language = 'scss';

	public function register(VisitorRegistry $registry): void
	{
		$registry->addLanguage(self::Language);

		$registry->addBefore($this->before(...), $registry::PriorityLanguage);
		$registry->addEnter($this->enter(...), $registry::PriorityLanguage);
		$registry->addLeave($this->leave(...), $registry::PriorityLanguage);
		$registry->addAfter($this->after(...), $registry::PriorityLanguage);
	}

	protected function before(ContentBuilder $builder, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder, 2);
	}

	protected function after(ContentBuilder $builder, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder);
	}

	protected function enter(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder);

		if ($value instanceof StructureValues) {
			return;
		}

		$builder->append("\$" . $value->getKebabCaseFullKey() . ": " . $value . ";");
	}

	protected function leave(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		if ($value instanceof StructureValues) {
			return;
		}

		$builder->newLine();
	}

}
