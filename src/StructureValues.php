<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

final class StructureValues extends BaseStructureValue
{

	/**
	 * @param array<StructureValue|self> $values
	 * @param array<string, mixed> $options
	 */
	public function __construct(
		private array $values = [],
		array $options = [],
	)
	{
		parent::__construct($options);

		foreach ($this->values as $value) {
			$value->parent = $this;
		}
	}

	/**
	 * @return iterable<StructureValue>
	 */
	public function getFlattenValues(): iterable
	{
		foreach ($this->values as $value) {
			if ($value instanceof self) {
				yield from $value->getFlattenValues();
			} else {
				yield $value;
			}
		}
	}

	/**
	 * @return array<StructureValue|StructureValues>
	 */
	public function getValues(): array
	{
		return $this->values;
	}

}
