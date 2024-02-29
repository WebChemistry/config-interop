<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Schema;

use Nette\Schema\Processor;
use Nette\Schema\Schema;
use stdClass;

final class SchemaProcessor
{

	public static function process(mixed $data, Schema $schema): mixed
	{
		return self::convertStdClassToArray((new Processor())->process($schema, $data));
	}

	private static function convertStdClassToArray(mixed $data): mixed
	{
		if (get_debug_type($data) === 'stdClass') {
			$data = (array) $data;
		}

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = self::convertStdClassToArray($value);
			}
		}

		return $data;
	}

}
