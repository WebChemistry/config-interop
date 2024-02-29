<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

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

		if (str_starts_with($value, '$') && str_ends_with($value, '$')) {
			$param = substr($value, 1, -1);

			if (array_key_exists($param, $this->parameters)) {
				return $this->parameters[$param];
			}
		}

		$return = preg_replace_callback('#\$([a-zA-Z\.0-9_]+)\$#', function (array $matches) {
			$match = $matches[1];

			if (array_key_exists($match, $this->parameters)) {
				return $this->parameters[$match];
			}

			return $match[0];
		}, $value);

		return $return === null ? $value : $return;
	}

}
