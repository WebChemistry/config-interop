<?php declare(strict_types = 1);

namespace WebChemistry\ConfigInterop\Content;

final class ContentBuilder
{

	private string $content = '';

	private bool $autoIndent = true;

	private int $level = 0;

	/** @var list<string> */
	private array $comments = [];

	public function __construct(
		private string $indent = "\t",
	)
	{
	}

	public function comment(string $comment): self
	{
		$this->comments[] = $comment;

		return $this;
	}

	/**
	 * @return list<string>
	 */
	public function getCommentsAndFlush(): array
	{
		$comments = $this->comments;
		$this->comments = [];

		return $comments;
	}

	public function increaseLevel(): void
	{
		$this->level++;
	}

	public function decreaseLevel(): void
	{
		$this->level--;
	}

	public function append(string $content): self
	{
		$this->content .= ($this->autoIndent ? $this->createIndent() : '') . $content;

		$this->autoIndent = false;

		return $this;
	}

	public function appendWithSpace(string $content): self
	{
		if (!$content) {
			return $this;
		}

		if (!$this->autoIndent) {
			$content = ' ' . $content;
		}

		$this->append($content);

		return $this;
	}

	public function ln(string $content, int $numberOfLines = 1): self
	{
		$this->content .= ($this->autoIndent ? $this->createIndent() : '') . $content . str_repeat("\n", $numberOfLines);

		$this->autoIndent = true;

		return $this;
	}

	public function newLine(int $numberOfLines = 1): self
	{
		$this->content .= str_repeat("\n", $numberOfLines);

		$this->autoIndent = true;

		return $this;
	}

	public function newLineIfNotExists(int $numberOfLines = 1): self
	{
		if ($this->autoIndent) {
			return $this;
		}

		$this->content .= str_repeat("\n", $numberOfLines);

		$this->autoIndent = true;

		return $this;
	}

	public function createIndent(): string
	{
		return str_repeat($this->indent, $this->level);
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function __toString(): string
	{
		return $this->content;
	}

}
