<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

use SetBased\Stratum\Common\Exception\RoutineLoaderException;

/**
 * Class for replacing placeholder with their actual values in stored routines.
 */
class PlaceholderHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object for escaping string values.
   *
   * @var EscapeHelper
   */
  private EscapeHelper $escaper;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The map from type hints to their actual data types.
   *
   * @var array
   */
  private array $placeholders = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param EscapeHelper $escaper
   */
  public function __construct(EscapeHelper $escaper)
  {
    $this->escaper = $escaper;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a placeholder with its actual value.
   *
   * @param string $name  The name of the placeholder.
   * @param string $value The actual value of the placeholder.
   * @param bool   $quote
   *
   * @return void
   */
  public function addPlaceholder(string $name, string $value, bool $quote): void
  {
    if ($quote)
    {
      $this->placeholders[$name] = "'".$this->escaper->escapeString($value)."'";
    }
    else
    {
      $this->placeholders[$name] = $value;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether a set of placeholders equals with the current placeholders and actual values.
   *
   * @param array $placeholders The set of placeholders.
   *
   * @return bool
   */
  public function comparePlaceholders(array $placeholders): bool
  {
    foreach ($placeholders as $placeholder => $value)
    {
      if (!isset($this->placeholders[$placeholder]) || $this->placeholders[$placeholder]!==$value)
      {
        return false;
      }
    }

    return true;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the placeholders in the source of a stored routine.
   *
   * @param StoredRoutineHelper $storedRoutine The source and metadata of a stored routine.
   */
  public function extractPlaceHolders(StoredRoutineHelper $storedRoutine): array
  {
    $placeholders = [];

    preg_match_all('/(@[A-Za-z0-9_.]+(%(max-)?type)?@)/',
                   $storedRoutine->getCode(),
                   $matches,
                   PREG_SET_ORDER);
    foreach ($matches as $match)
    {
      $placeholder = $match[0];
      if (!isset($this->placeholders[$placeholder]))
      {
        throw new RoutineLoaderException("Unknown placeholder '%s' in file %s.",
                                         $placeholder,
                                         $storedRoutine->getPath());
      }
      $placeholders[$placeholder] = $this->placeholders[$placeholder];
    }

    return $placeholders;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Substitutes all replace pairs in the source of the stored routine.
   *
   * @param array $codeLines The source of the stored routine as an array of lines.
   */
  public function substitutePlaceholders(array $codeLines): string
  {
    $newCodeLines = [];
    foreach ($codeLines as $i => $line)
    {
      $this->placeholders['__LINE__'] = $i + 1;
      $newCodeLines[$i]               = strtr($line, $this->placeholders);
    }

    return implode(PHP_EOL, $newCodeLines);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
