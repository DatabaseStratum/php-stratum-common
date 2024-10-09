<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

use SetBased\Stratum\Common\Exception\RoutineLoaderException;
use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;

/**
 * Class for replacing type hints with their actual data types in stored routines.
 */
class TypeHintHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The map from type hints to their actual data types.
   *
   * @var array
   */
  private array $typeHints = [];

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * Adds a type hint with its actual data type.
   *
   * @param string $typeHint The type hint.
   * @param string $dataType The actual data type of the type hint.
   *
   * @return void
   */
  public function addTypeHint(string $typeHint, string $dataType): void
  {
    $this->typeHints[$typeHint] = $dataType;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Aligns the type hints in the source of the stored routine.
   *
   * @param string[] $codeLines The source of the stored routine as an array of lines.
   *
   * @return string[]
   */
  public function alignTypeHints(array $codeLines): array
  {
    $blocks = [];
    $start  = null;
    $length = 0;
    foreach ($codeLines as $index => $line)
    {
      $n = preg_match('/--\s+type:\s+.*$/', $line, $matches);
      if ($n!==0)
      {
        if ($start===null)
        {
          $start = $index;
        }
        $length = max($length, mb_strlen($line) - mb_strlen($matches[0]) + 2);
      }
      else
      {
        if ($start!==null)
        {
          $blocks[] = ['first' => $start, 'last' => $index, 'length' => $length];
          $start    = null;
          $length   = 0;
        }
      }
    }

    foreach ($blocks as $block)
    {
      for ($index = $block['first']; $index<$block['last']; $index++)
      {
        preg_match('/\s+type:\s+.*$/', $codeLines[$index], $matches);
        $leftPart = mb_substr($codeLines[$index], 0, -mb_strlen($matches[0]));
        $leftPart = $leftPart.str_repeat(' ', $block['length'] - mb_strlen($leftPart) + 1);

        $codeLines[$index] = $leftPart.ltrim($matches[0]);
      }
    }

    return $codeLines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether a set of type hints equals with the current type hints and actual data types.
   *
   * @param array $typeHints The set of type hints.
   *
   * @return bool
   */
  public function compareTypeHints(array $typeHints): bool
  {
    foreach ($typeHints as $typeHint => $dataType)
    {
      if (!isset($this->typeHints[$typeHint]) || $this->typeHints[$typeHint]!==$dataType)
      {
        return false;
      }
    }

    return true;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type hints in the source of a stored routine.
   *
   * @param StoredRoutineHelper $storedRoutine The source and metadata of a stored routine.
   *
   * @return array
   */
  public function extractTypeHints(StoredRoutineHelper $storedRoutine): array
  {
    $typeHints = [];

    preg_match_all('/(\s+--\s+type:\s+(?<type_hint>(\w+\.)?\w+\.\w+(%max)?))/',
                   $storedRoutine->getCode(),
                   $matches,
                   PREG_SET_ORDER);
    foreach ($matches as $match)
    {
      $typeHint = $match['type_hint'];
      if (!isset($this->typeHints[$typeHint]))
      {
        throw new RoutineLoaderException("Unknown type hint '%s' in file %s", $typeHint, $storedRoutine->getPath());
      }
      $typeHints[$typeHint] = $this->typeHints[$typeHint];
    }

    return $typeHints;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates types in the source of the stored routine according to the type hints.
   *
   * @param array                $codeLines      The source of the stored routine as an array of lines.
   * @param CommonDataTypeHelper $dataTypeHelper The data type helper.
   *
   * @return array
   */
  public function updateTypes(array $codeLines, CommonDataTypeHelper $dataTypeHelper): array
  {
    $parts   = ['whitespace' => '(?<whitespace>\s+)',
                'type_list'  => str_replace('type-list',
                                            implode('|', $dataTypeHelper->allColumnTypes()),
                                            '(?<datatype>(type-list))(?<extra>.*)?'),
                'hint'       => '(?<hint>\s+--\s+type:\s+(\w+\.)?\w+\.\w+(%max)?\s*)'];
    $pattern = '/'.implode('', $parts).'$/i';

    foreach ($codeLines as $index => $line)
    {
      if (preg_match('/'.$parts['hint'].'/i', $line))
      {
        $n = preg_match($pattern, $line, $matches);
        if ($n===0)
        {
          throw new RoutineLoaderException("Found a type hint at line %d, but unable to find data type.", $index + 1);
        }

        $hint = trim(preg_replace('/\s+--\s+type:\s+/i', '', $matches['hint']));
        if (!isset($this->typeHints[$hint]))
        {
          throw new RoutineLoaderException("Unknown type hint '%s' found at line %d.", $hint, $index + 1);
        }

        if (preg_match('/(?<extra1> not\s+null)?\s*(?<extra2> default.+)?\s*(?<extra3>[;,]\s*)?$/i',
                       $matches['extra'],
                       $other,
                       PREG_UNMATCHED_AS_NULL))
        {
          $extra1 = $other['extra1'] ?? '';
          $extra2 = $other['extra2'] ?? '';
          $extra3 = $other['extra3'] ?? '';
        }
        else
        {
          $extra1 = '';
          $extra2 = '';
          $extra3 = '';
        }

        $actualType = $this->typeHints[$hint];
        $newLine    = sprintf('%s%s%s%s%s%s%s',
                              mb_substr($line, 0, -mb_strlen($matches[0])),
                              $matches['whitespace'],
                              $actualType, // <== the real replacement
                              $extra1,
                              $extra2,
                              $extra3,
                              $matches['hint']);

        if (str_replace(' ', '', $line)!==str_replace(' ', '', $newLine))
        {
          $codeLines[$index] = $newLine;
        }
      }
    }

    return $codeLines;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
