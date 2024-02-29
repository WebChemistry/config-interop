<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Feature;

use WebChemistry\ConfigInterop\Content\ContentBuilder;
use WebChemistry\ConfigInterop\Visitor\Visitor;
use WebChemistry\ConfigInterop\Visitor\VisitorRegistry;

final class FileCommentFeature implements Visitor
{

	public function __construct(
		private string $comment = 'This file is auto-generated. Do not edit!',
	)
	{
	}

	public function register(VisitorRegistry $registry): void
	{
		$registry->addBefore($this->start(...), $registry::PriorityBeforeLanguageHigh);
	}

	private function start(ContentBuilder $builder): void
	{
		$builder->comment($this->comment);
	}

}
