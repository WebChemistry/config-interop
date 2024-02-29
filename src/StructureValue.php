<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

final class StructureValue extends BaseStructureValue
{

	public readonly string|int|float|bool|null $value;

	/**
	 * @param array<string, mixed> $options
	 */
	public function __construct(
		StructureValue|string|int|float|bool|null $value,
		array $options = [],
	)
	{
		if ($value instanceof StructureValue) {
			$this->value = $value->value;
			$options = array_merge($value->getOptions()->all(), $options);
		} else {
			$this->value = $value;
		}

		parent::__construct($options);
	}

	public function __toString(): string
	{
		return (string) $this->value;
	}

}
