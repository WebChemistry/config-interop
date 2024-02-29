<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Context;

use WebChemistry\ConfigInterop\Option\Options;

final class GeneratorContext extends Options
{

	public const Id = 'id';
	public const Language = 'language';

	/**
	 * @param mixed[] $values
	 */
	public function __construct(
		array $values = [],
	)
	{
		parent::__construct($values);
	}

	public function getId(): string|int
	{
		return $this->getStringOrInt(self::Id);
	}

	public function getLanguage(): string
	{
		return $this->getString(self::Language);
	}

}
