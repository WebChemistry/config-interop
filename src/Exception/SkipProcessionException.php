<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Exception;

use LogicException;
use Throwable;

final class SkipProcessionException extends LogicException
{

	public function __construct(string $message = 'Procession skipped.', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}
