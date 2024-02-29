<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

use Nette\Neon\Entity;
use WebChemistry\ConfigInterop\Function\ConfigFunctions;

final class StructureValueBuilder
{

	private ConfigFunctions $functions;

	/**
	 * @param mixed[] $options
	 * @param mixed[] $inheritedValueOptions
	 * @param array<StructureValue|StructureValues> $values
	 * @param mixed[] $original
	 */
	public function __construct(
		private array $original,
		private array $values = [],
		private array $options = [],
		private array $inheritedValueOptions = [],
		?ConfigFunctions $functions = null,
	)
	{
		$this->functions = $functions ?? new ConfigFunctions([]);
	}

	/**
	 * @return mixed[]
	 */
	public function getOriginal(bool $includeInternal = true): array
	{
		if ($includeInternal) {
			return $this->original;
		} else {
			return array_filter($this->original, fn ($key) => !str_starts_with($key, '_'), ARRAY_FILTER_USE_KEY);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function getInheritedValueOptions(): array
	{
		return $this->inheritedValueOptions;
	}

	public function setOriginalValue(int|string $key, mixed $item): self
	{
		$this->original[$key] = $item;

		return $this;
	}

	public function removeFromOriginal(string|int $key): self
	{
		unset($this->original[$key]);

		return $this;
	}

	public function setValue(string|int $key, StructureValue|StructureValues $value): self
	{
		$this->values[$key] = $value;

		return $this;
	}

	public function addValue(string $key, StructureValue|StructureValues $value): self
	{
		if (isset($this->values[$key])) {
			return $this;
		}

		$value->key = $key;

		$this->values[$key] = $value;

		return $this;
	}

	public function setOption(string $key, mixed $value): self
	{
		$this->options[$key] = $value;

		return $this;
	}

	public function setInheritedValueOption(string $key, mixed $value): self
	{
		$this->inheritedValueOptions[$key] = $value;

		return $this;
	}

	public function addOption(string $key, mixed $value): self
	{
		if (isset($this->options[$key])) {
			return $this;
		}

		$this->options[$key] = $value;

		return $this;
	}

	public function build(): StructureValues
	{
		return new StructureValues($this->values, $this->options);
	}

	/**
	 * @param array<string, mixed> $options
	 */
	public function createValue(Entity|string|int|float|bool|null $value, array $options = []): StructureValue
	{
		if ($value instanceof Entity) {
			return $this->functions->callByEntity($value);
		}

		return new StructureValue($value, array_merge($options, $this->inheritedValueOptions));
	}

}
