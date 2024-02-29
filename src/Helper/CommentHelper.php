<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Helper;

use WebChemistry\ConfigInterop\Content\ContentBuilder;

final class CommentHelper
{

	public static function flushMultilineComments(ContentBuilder $builder, int $numberOfNewLinesAfter = 1): void
	{
		$comments = $builder->getCommentsAndFlush();

		if (!$comments) {
			return;
		}

		$builder->append('/**');

		if (count($comments) === 1) {
			$builder->append(' ' . $comments[0]);
			$builder->append(' */');
		} else {
			$builder->newLineIfNotExists();

			foreach ($comments as $comment) {
				$builder->append(' * ' . $comment);
				$builder->newLine();
			}
			$builder->append(' */');
		}

		if ($numberOfNewLinesAfter > 0) {
			$builder->newLine($numberOfNewLinesAfter);
		}
	}

}
