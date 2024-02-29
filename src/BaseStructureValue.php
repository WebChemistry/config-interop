<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop;

use LogicException;
use WebChemistry\ConfigInterop\Helper\TextCaseHelper;
use WebChemistry\ConfigInterop\Option\Options;

abstract class BaseStructureValue
{

	public ?string $key = null;

	public ?StructureValues $parent = null;

	private Options $options;

	/**
	 * @param array<string, mixed> $options
	 */
	public function __construct(array $options = [])
	{
		$this->options = new Options($options);
	}

	public function getCamelCaseKey(): ?string
	{
		return $this->key;
	}

	public function getKebabCaseKey(): ?string
	{
		return $this->key ? TextCaseHelper::toKebabCase($this->key) : null;
	}

	public function getKebabCaseFullKey(): string
	{
		$keys = [];
		$parent = $this;

		do {
			if ($key = $parent->getKebabCaseKey()) {
				array_unshift($keys, $key);
			}

			$parent = $parent->parent;
		} while ($parent);

		return implode('-', $keys);
	}

	public function getCamelCaseFullKey(): string
	{
		$keys = [];
		$parent = $this;

		do {
			if ($key = $parent->getCamelCaseKey()) {
				array_unshift($keys, $key);
			}

			$parent = $parent->parent;
		} while ($parent);

		return implode('', array_map(ucfirst(...), $keys));
	}

	public function getOptions(): Options
	{
		return $this->options;
	}

}
