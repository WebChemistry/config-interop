<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

use LogicException;

final class Parameters
{

	/**
	 * @param array<string, scalar|null> $parameters
	 */
	public function __construct(
		private array $parameters,
	)
	{
	}

	public function setParameter(string $name, string|int|float|bool|null $value): void
	{
		$this->parameters[$name] = $value;
	}

	public function expand(mixed $value): mixed
	{
		if (!is_string($value)) {
			return $value;
		}

		if (str_starts_with($value, '%') && str_ends_with($value, '%') && $value[1] !== '%') {
			$param = substr($value, 1, -1);

			if (!array_key_exists($param, $this->parameters)) {
				throw new LogicException(sprintf('Parameter %%%s%% not found', $param));
			}

			return $this->parameters[$param];
		}

		$return = preg_replace_callback('#(?<!%)%([a-zA-Z\.0-9_]+)%#', function (array $matches) {
			$match = $matches[1];

			if (!array_key_exists($match, $this->parameters)) {
				throw new LogicException(sprintf('Parameter %%%s%% not found', $match));
			}

			return $this->parameters[$match];
		}, $value);

		return $return === null ? $value : $return;
	}

	/**
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function expandArray(array $values): array
	{
		$return = [];

		foreach ($values as $key => $value) {
			if (is_array($value)) {
				$return[$key] = $this->expandArray($value);

				continue;
			}

			$return[$key] = $this->expand($value);
		}

		return $return;
	}

}
