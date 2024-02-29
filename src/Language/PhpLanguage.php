<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Language;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\Helper\CommentHelper;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValues;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class PhpLanguage implements Visitor
{

	public const Language = 'php';

	private ?string $class = null;

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

		$this->class = $context->getStringOrNull('className');

		$builder->ln('<?php declare(strict_types = 1);', 2);

		CommentHelper::flushMultilineComments($builder, 2);

		if ($this->class) {
			if ($namespace = $context->getStringOrNull('namespace')) {
				$builder->ln(sprintf('namespace %s;', $namespace), 2);
			}

			$builder->ln(sprintf('class %s', $this->class));
			$builder->ln('{');
			$builder->increaseLevel();
		} else {
			$builder->ln('return [');
			$builder->increaseLevel();
		}
	}

	protected function enter(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		CommentHelper::flushMultilineComments($builder);

		if ($this->class) {
			if ($value instanceof StructureValue) {
				$builder->append(sprintf('public const %s = %s;', $value->getCamelCaseFullKey(), var_export($value->value, true)));
			}
		} else {
			if ($value instanceof StructureValues) {
				$builder->ln(sprintf("'%s' => [", $value->key));
				$builder->increaseLevel();
			} else {
				$builder->append(sprintf("'%s' => %s,", $value->key, var_export($value->value, true)));
			}
		}
	}

	protected function leave(ContentBuilder $builder, StructureValues|StructureValue $value, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		if ($this->class) {
			if ($value instanceof StructureValue) {
				$builder->newLine();
			}
		} else {
			if ($value instanceof StructureValues) {
				$builder->decreaseLevel();
				$builder->ln('],');
			} else {
				$builder->newLine();
			}
		}
	}

	protected function end(ContentBuilder $builder, GeneratorContext $context): void
	{
		if ($context->getLanguage() !== self::Language) {
			return;
		}

		$builder->decreaseLevel();
		$builder->ln($this->class ? '}' : '];');
	}

}
