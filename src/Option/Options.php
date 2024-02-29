<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Option;

use LogicException;

class Options
{

	/**
	 * @param mixed[] $values
	 */
	public function __construct(
		protected array $values = [],
	)
	{
	}

	public function with(string|int $key, mixed $value): static
	{
		$new = clone $this;
		$new->values[$key] = $value;

		return $new;
	}

	public function get(string|int $key): mixed
	{
		return $this->values[$key] ?? null;
	}

	public function getRequired(string|int $key): mixed
	{
		if (!$this->has($key)) {
			throw new LogicException(sprintf('Value of "%s" does not exist.', $key));
		}

		return $this->values[$key];
	}

	public function has(string|int $key): bool
	{
		return array_key_exists($key, $this->values);
	}

	public function getString(string|int $key): string
	{
		$value = $this->getRequired($key);

		if (!is_string($value)) {
			throw new LogicException(sprintf('Value of "%s" is not a string.', $key));
		}

		return $value;
	}

	public function getStringOrNull(string|int $key): ?string
	{
		$value = $this->get($key);

		if ($value === null) {
			return null;
		}

		if (!is_string($value)) {
			throw new LogicException(sprintf('Value of "%s" is not a string.', $key));
		}

		return $value;
	}

	public function getStringOrInt(string|int $key): string|int
	{
		$value = $this->getRequired($key);

		if (!is_string($value) || !is_int($key)) {
			throw new LogicException(sprintf('Value of "%s" is not string or int.', $key));
		}

		return $value;
	}

	/**
	 * @return mixed[]
	 */
	public function all(): array
	{
		return $this->values;
	}

}
