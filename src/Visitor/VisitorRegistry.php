<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Visitor;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Context\GeneratorContext;
use WebChemistry\ConfigInterop\StructureValue;
use WebChemistry\ConfigInterop\StructureValues;

final class VisitorRegistry
{

	public const PriorityLanguage = 50;

	public const PriorityBeforeLanguageLow = 40;
	public const PriorityBeforeLanguage = 30;
	public const PriorityBeforeLanguageHigh = 20;

	public const PriorityAfterLanguageHigh = 60;
	public const PriorityAfterLanguage = 70;
	public const PriorityAfterLanguageLow = 80;

	/** @var array<int, (callable(ContentBuilder $builder, GeneratorContext $context): void)[]> */
	private array $before = [];

	/** @var array<int, (callable(ContentBuilder $builder, GeneratorContext $context): void)[]> */
	private array $after = [];

	/** @var array<int, (callable(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void)[]> */
	private array $enter = [];

	/** @var array<int, (callable(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void)[]> */
	private array $leave = [];

	/** @var array<string, bool> */
	private array $sorted = [
		'before' => true,
		'after' => true,
		'enter' => true,
		'leave' => true,
	];

	/** @var array<string, true> */
	private array $languages = [];

	/** @var (callable(VisitorRegistry $registry, GeneratorContext $context): void)[] */
	private array $dynamic = [];

	/** @var (callable(GeneratorContext $context): GeneratorContext)[] */
	private array $initialize = [];

	public static function priorityBefore(int $amount): int
	{
		return self::PriorityLanguage - $amount;
	}

	public static function priorityAfter(int $amount): int
	{
		return self::PriorityLanguage + $amount;
	}

	public function createByContext(GeneratorContext $context): self
	{
		if (!$this->dynamic) {
			return $this;
		}

		$registry = clone $this;

		foreach ($this->dynamic as $callback) {
			$callback($registry, $context);
		}

		return $registry;
	}

	/**
	 * @param callable(GeneratorContext $context): GeneratorContext $callback
	 */
	public function addInitialize(callable $callback): self
	{
		$this->initialize[] = $callback;

		return $this;
	}

	/**
	 * @param callable(VisitorRegistry $registry, GeneratorContext $context): void $callback
	 */
	public function addDynamic(callable $callback): self
	{
		$this->dynamic[] = $callback;

		return $this;
	}

	public function addLanguage(string $language): self
	{
		$this->languages[$language] = true;

		return $this;
	}

	/**
	 * @param callable(ContentBuilder $builder, GeneratorContext $context): void $callback
	 */
	public function addBefore(callable $callback, int $priority = self::PriorityBeforeLanguage): self
	{
		$this->before[$priority][] = $callback;
		$this->sorted['before'] = false;

		return $this;
	}

	/**
	 * @param callable(ContentBuilder $builder, GeneratorContext $context): void $callback
	 */
	public function addAfter(callable $callback, int $priority = self::PriorityAfterLanguage): self
	{
		$this->after[$priority][] = $callback;
		$this->sorted['after'] = false;

		return $this;
	}

	/**
	 * @param callable(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void $callback
	 */
	public function addEnter(callable $callback, int $priority = self::PriorityAfterLanguage): self
	{
		$this->enter[$priority][] = $callback;
		$this->sorted['enter'] = false;

		return $this;
	}

	/**
	 * @param callable(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void $callback
	 */
	public function addLeave(callable $callback, int $priority = self::PriorityBeforeLanguage): self
	{
		$this->leave[$priority][] = $callback;
		$this->sorted['leave'] = false;

		return $this;
	}

	private function sort(): void
	{
		if (!$this->sorted['before']) {
			ksort($this->before);

			$this->sorted['before'] = true;
		}

		if (!$this->sorted['after']) {
			ksort($this->after);

			$this->sorted['after'] = true;
		}

		if (!$this->sorted['enter']) {
			ksort($this->enter);

			$this->sorted['enter'] = true;
		}

		if (!$this->sorted['leave']) {
			ksort($this->leave);

			$this->sorted['leave'] = true;
		}
	}

	public function hasLanguage(string $language): bool
	{
		return isset($this->languages[$language]);
	}

	public function before(ContentBuilder $builder, GeneratorContext $context): void
	{
		$this->sort();

		foreach ($this->before as $callbacks) {
			foreach ($callbacks as $callback) {
				$callback($builder, $context);
			}
		}
	}

	public function enter(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void
	{
		$this->sort();

		foreach ($this->enter as $callbacks) {
			foreach ($callbacks as $callback) {
				$callback($builder, $value, $context);
			}
		}
	}

	public function leave(ContentBuilder $builder, StructureValue|StructureValues $value, GeneratorContext $context): void
	{
		$this->sort();

		foreach ($this->leave as $callbacks) {
			foreach ($callbacks as $callback) {
				$callback($builder, $value, $context);
			}
		}
	}

	public function after(ContentBuilder $builder, GeneratorContext $context): void
	{
		$this->sort();

		foreach ($this->after as $callbacks) {
			foreach ($callbacks as $callback) {
				$callback($builder, $context);
			}
		}
	}

	public function initialize(GeneratorContext $context): GeneratorContext
	{
		foreach ($this->initialize as $callback) {
			$context = $callback($context);
		}

		return $context;
	}

}
