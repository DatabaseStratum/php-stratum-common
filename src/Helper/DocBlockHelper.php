<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Helper;

/**
 * Utility class for processing parts of  DocBlock.
 */
class DocBlockHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Map from tag name to the expected number of parameters for that tag.
   *
   * @var array[string,int]
   */
  private static $tagParameters = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract the tag, its parameters, and description from raw data.
   *
   * @param array $lines The raw data of the tag.
   *
   * @return array
   */
  public static function extractTag(array $lines): array
  {
    $parts = preg_split('/ +/', trim($lines[0]));

    $tag = ['tag'         => ltrim($parts[0], '@'),
            'arguments'   => [],
            'description' => []];
    array_shift($parts);

    $n = self::$tagParameters[$tag['tag']] ?? 0;
    for ($i = 0; $i<$n; $i++)
    {
      $tag['arguments'][] = (!empty($parts)) ? array_shift($parts) : '';
    }

    $line0 = $lines[0];
    $count = 1;
    $line0 = str_replace('@'.$tag['tag'], str_repeat(' ', mb_strlen($tag['tag']) + 1), $line0, $count);
    foreach ($tag['arguments'] as $param)
    {
      if ($param!=='')
      {
        $count = 1;
        $line0 = str_replace($param, str_repeat(' ', mb_strlen($param)), $line0, $count);
      }
    }
    $lines[0] = $line0;

    $tag['description'] = self::leftTrimBlock($lines);

    return $tag;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Left trims whitespace of a block of lines. No line is trimmed more than the first line.
   *
   * @param array $lines The block of lines.
   *
   * @return array
   */
  public static function leftTrimBlock(array $lines): array
  {
    if (empty($lines)) return $lines;

    $length1 = mb_strlen($lines[0]);
    $length2 = mb_strlen(ltrim($lines[0]));
    $diff    = $length1 - $length2;

    $ret = [];
    foreach ($lines as $line)
    {
      $ret [] = static::leftTrimMax($line, $diff);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Set the expected number of parameter of a tag
   *
   * @param string $tagName            The name of the taf.
   * @param int    $numberOfParameters The expected number of parameters.
   */
  public static function setTagParameters(string $tagName, int $numberOfParameters): void
  {
    $tagName = ltrim($tagName, '@');
    if ($numberOfParameters<=0)
    {
      unset(self::$tagParameters[$tagName]);
    }
    else
    {
      self::$tagParameters[$tagName] = $numberOfParameters;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Left trims at most a certain number of whitespace characters from the beginning of a string.
   *
   * @param string $string The string.
   * @param int    $max    The maximum number of  whitespace characters to be trimmed of.
   *
   * @return string
   */
  private static function leftTrimMax(string $string, int $max): string
  {
    $length1 = mb_strlen($string);
    $string  = ltrim($string);
    $length2 = mb_strlen($string);
    $diff    = $length1 - $length2;
    if ($diff>$max)
    {
      $string = str_repeat(' ', $diff - $max).$string;
    }

    return $string;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
