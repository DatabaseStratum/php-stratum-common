<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

/**
 * A simple DocBlock reflection.
 */
class DocBlockReflection
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The comment as an array of strings.
   *
   * @var string[]
   */
  private array $comment;

  /**
   * The description.
   *
   * @var string[]
   */
  private array $description;

  /**
   * The tags in the DocBlock
   *
   * @var array
   */
  private array $tags;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string[] $comment The comment as an array of strings.
   */
  public function __construct(array $comment)
  {
    $this->comment = $comment;

    $this->reflect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes leading empty lines from a list of lines.
   *
   * @param array $lines The lines.
   */
  private static function removeLeadingEmptyLines(array $lines): array
  {
    $tmp   = [];
    $empty = true;
    foreach ($lines as $line)
    {
      $empty = ($empty && $line==='');
      if (!$empty)
      {
        $tmp[] = $line;
      }
    }

    return $tmp;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes leading empty lines from a list of lines.
   *
   * @param array $lines The lines.
   */
  private static function removeTrailingEmptyLines(array $lines): array
  {
    $lines = array_reverse($lines);
    $lines = self::removeLeadingEmptyLines($lines);

    return array_reverse($lines);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the description.
   */
  public function getDescription(): array
  {
    return $this->description;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a list of tags.
   *
   * @param string $name The name of the tag.
   */
  public function getTags(string $name): array
  {
    $tags = [];
    foreach ($this->tags as $tag)
    {
      if ($tag[0]===$name)
      {
        $tags[] = $tag[1];
      }
    }

    return $tags;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Cleans the DocBlock from leading and trailing white space and comment tokens.
   */
  private function cleanDocBlock(): void
  {
    if (empty($this->comment))
    {
      return;
    }

    for ($index = 1; $index<count($this->comment) - 1; $index++)
    {
      $this->comment[$index] = preg_replace('|^\s*\*|', '', $this->comment[$index]);
    }
    $this->comment[0]                          = preg_replace('|^\s*/\*\*|', '', $this->comment[0]);
    $this->comment[sizeof($this->comment) - 1] = preg_replace('|\*/\s*$|', '', end($this->comment));

    foreach ($this->comment as $index => $line)
    {
      $this->comment[$index] = trim($line);
    }

    $this->comment = self::removeLeadingEmptyLines($this->comment);
    $this->comment = self::removeTrailingEmptyLines($this->comment);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the description from the DocBlock. The description starts at the first line and stops at the first tag
   * or the end of the DocBlock.
   */
  private function extractDescription(): void
  {
    $tmp = [];
    foreach ($this->comment as $line)
    {
      if (str_starts_with($line, '@'))
      {
        break;
      }
      $tmp[] = $line;
    }

    $this->description = self::removeTrailingEmptyLines($tmp);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract tags from the DocBlock.
   */
  private function extractTags(): void
  {
    $this->tags = [];
    $current    = false;
    foreach ($this->comment as $line)
    {
      $n = preg_match('|^@(\w+)|', $line, $parts);
      if ($n===1)
      {
        $current      = true;
        $this->tags[] = [$parts[1], []];
      }

      if ($current)
      {
        if ($line==='')
        {
          $current = false;
        }
        else
        {
          $this->tags[sizeof($this->tags) - 1][1][] = $line;
        }
      }
    }

    foreach ($this->tags as $index => $tag)
    {
      $this->tags[$index][1] = implode(PHP_EOL, $tag[1]);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Parses the DocBlock.
   */
  private function reflect(): void
  {
    $this->cleanDocBlock();
    $this->extractDescription();
    $this->extractTags();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
