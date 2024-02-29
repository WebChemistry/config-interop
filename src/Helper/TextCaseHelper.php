<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Helper;

use RuntimeException;

final class TextCaseHelper
{

	public static function toKebabCase(string $str): string
	{
		$str = preg_replace('/(?<!^)[A-Z]/', '-$0', $str);

		if ($str === null) {
			throw new RuntimeException('Error occurred while converting to kebab case');
		}

		return strtolower($str);
	}

}
