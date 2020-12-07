<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Helper;

/**
 * Utility class for processing parts of a DocBlock.
 */
class DocBlockHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Map from tag name to the expected number of parameters for that tag.
   *
   * @var array<string,int>
   */
  private static array $tagParameters = [];

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

    $names = self::$tagParameters[$tag['tag']] ?? [];
    foreach ($names as $name)
    {
      $tag['arguments'][$name] = (!empty($parts)) ? array_shift($parts) : '';
    }

    $find    = '@'.$tag['tag'];
    $replace = str_repeat(' ', mb_strlen($find));
    $pattern = '/'.preg_quote($find, '/').'/';
    $line0   = preg_replace($pattern, $replace, $lines[0], 1);
    foreach ($tag['arguments'] as $param)
    {
      if ($param!=='')
      {
        $replace = str_repeat(' ', mb_strlen($param));
        $pattern = '/'.preg_quote($param, '/').'/';
        $line0   = preg_replace($pattern, $replace, $line0, 1);
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
   * Sets the names of the parameters of a tag.
   *
   * @param string   $tagName The name of the tag.
   * @param string[] $names   The names of the parameters
   */
  public static function setTagParameters(string $tagName, array $names): void
  {
    $tagName = ltrim($tagName, '@');
    if (empty($names))
    {
      unset(self::$tagParameters[$tagName]);
    }
    else
    {
      self::$tagParameters[$tagName] = $names;
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
