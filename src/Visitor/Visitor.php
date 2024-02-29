<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Visitor;

interface Visitor
{

	public function register(VisitorRegistry $registry): void;

}
