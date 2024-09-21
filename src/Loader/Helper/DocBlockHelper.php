<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

use SetBased\Stratum\Common\Exception\RoutineLoaderException;

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

  /**
   * The designation of the stored routine.
   *
   * @var array|null
   */
  private ?array $designation = null;

  /**
   * The DocBlock reflection object.
   *
   * @var DocBlockReflection
   */
  private DocBlockReflection $docBlockReflection;

  /**
   * Whether param tags have types.
   *
   * @var bool
   */
  private bool $paramTagsHaveTypes = false;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct(StoredRoutineHelper $routineSource)
  {
    $this->docBlockReflection = self::createDocBlockReflection($routineSource);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extract the tag, its parameters, and description from raw data.
   *
   * @param string[] $lines The raw data of the tag.
   *
   * @return array<string,mixed>
   */
  public static function extractTag(array $lines): array
  {
    $parts = preg_split('/ +/', trim($lines[0]));

    $tag = ['tag'       => ltrim($parts[0], '@'),
            'arguments' => []];
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
   * @param string[] $lines The block of lines.
   *
   * @return string[]
   */
  public static function leftTrimBlock(array $lines): array
  {
    if (empty($lines))
    {
      return $lines;
    }

    $length1 = mb_strlen($lines[0]);
    $length2 = mb_strlen(ltrim($lines[0]));
    $diff    = $length1 - $length2;

    $ret = [];
    foreach ($lines as $line)
    {
      $ret[] = self::leftTrimMax($line, $diff);
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
   * Creates the DocBlock reflection object.
   *
   * @param StoredRoutineHelper $routineSource The routine source helper.
   */
  private static function createDocBlockReflection(StoredRoutineHelper $routineSource): DocBlockReflection
  {
    [$line1, $line2] = self::getDocBlockLines($routineSource);
    $lines = array_slice($routineSource->getCodeLines(), $line1, $line2 - $line1 + 1);

    return new DocBlockReflection($lines);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the start and end line of the DocBlock of the stored routine code.
   *
   * @param StoredRoutineHelper $routineSource The routine source helper.
   */
  private static function getDocBlockLines(StoredRoutineHelper $routineSource): array
  {
    $line1 = null;
    $line2 = null;

    foreach ($routineSource->getCodeLines() as $index => $line)
    {
      if (preg_match('/^\s*\/\*\*\s*$/', $line))
      {
        $line1 = $index;
      }

      if (preg_match('/^\s*\*\/\s*$/', $line))
      {
        $line2 = $index;
        break;
      }
    }

    if ($line1===null || $line2===null)
    {
      throw new RoutineLoaderException('No DocBlock found in %s.', $routineSource->getPath());
    }

    return [$line1, $line2];
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
  /**
   * Returns the description of the stored routine.
   */
  public function getDescription(): array
  {
    return $this->docBlockReflection->getDescription();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the designation of the stored routine.
   */
  public function getDesignation(): array
  {
    if ($this->designation===null)
    {
      $tags = $this->docBlockReflection->getTags('type');
      if (count($tags)===0)
      {
        throw new RoutineLoaderException('Tag @type not found in DocBlock.');
      }
      elseif (count($tags)>1)
      {
        throw new RoutineLoaderException('Multiple @type tags found in DocBlock.');
      }

      $tag = $tags[0];
      $n   = preg_match('/^(@type)\s+(?<type>\w+)\s*(?<extra>.*)/', $tag, $parts1);
      if ($n!==1)
      {
        throw new RoutineLoaderException('Found @type tag without designation type. Found: %s', $tag);
      }
      $this->designation['type'] = $parts1['type'];
      switch ($this->designation['type'])
      {
        case 'rows_with_key':
        case 'rows_with_index':
          if (trim($parts1['extra'])==='')
          {
            throw new RoutineLoaderException('Designation type %s requires a list of columns. Found: %s',
                                             $this->designation['type'],
                                             $tag);
          }
          $this->designation['columns'] = preg_split('/[,\t\n ]+/', $parts1['extra']);
          break;

        case 'insert_multiple':
          $n = preg_match('/^(?P<table_name>\w+)\s*(?P<keys>.+)/', $parts1['extra'], $parts2);
          if ($n!==1)
          {
            throw new RoutineLoaderException('Designation type insert_multiple requires a table name and a list of keys. Found: %s',
                                             $tag);
          }
          $this->designation['table_name'] = $parts2['table_name'];
          $this->designation['keys']       = preg_split('/[,\t\n ]+/', $parts2['keys']);
          break;

        case 'singleton0':
        case 'singleton1':
        case 'function':
          if (trim($parts1['extra'])==='')

          {
            throw new RoutineLoaderException('Designation type %s requires a list of return rdbmsTypes. Found: %s',
                                             $this->designation['type'],
                                             $tag);
          }
          $this->designation['return'] = preg_split('/[,\t\n| ]+/', $parts1['extra']);
          break;

        default:
          if (trim($parts1['extra'])!=='')
          {
            throw new RoutineLoaderException('Superfluous data found after designation type %s. Found: %s',
                                             $this->designation['type'],
                                             $tag);
          }
      }
    }

    return $this->designation;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether param tags have types.
   */
  public function getParamTagsHaveTypes(): bool
  {
    return $this->paramTagsHaveTypes;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the description of a parameter as found in the DocBlock of the stored routine.
   *
   * @param string $name The name of the parameter.
   */
  public function getParameterDescription(string $name): ?string
  {
    foreach ($this->getParameters() as $parameter)
    {
      if ($parameter['name']===$name)
      {
        return $parameter['description'];
      }
    }

    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameters as found in the DocBlock of the stored routine.
   *
   * @return array
   */
  public function getParameters(): array
  {
    $parameters = [];
    foreach ($this->docBlockReflection->getTags('param') as $tag)
    {
      if ($this->paramTagsHaveTypes)
      {
        $n = preg_match('/^(@param)\s+(?<type>\w+)\s+:?(?<name>\w+)\s*(?<description>.+)?/s', $tag, $matches);
        if ($n===1)
        {
          $parameters[] = ['name'        => $matches['name'],
                           'type'        => $matches['type'],
                           'description' => $matches['description']];
        }
      }
      else
      {
        $n = preg_match('/^(@param)\s+(?<name>\w+)\s*(?<description>.+)?/s', $tag, $matches, PREG_UNMATCHED_AS_NULL);
        if ($n===1)
        {
          $parameters[] = ['name'        => $matches['name'],
                           'description' => $matches['description']];
        }
      }
    }

    return $parameters;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a list of tags.
   *
   * @param string $name The name of the tag.
   */
  public function getTags(string $name): array
  {
    return $this->docBlockReflection->getTags($name);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Set whether param tags have types.
   *
   * @param bool $paramTagsHaveTypes Whether param tags have types.
   */
  public function setParamTagsHaveTypes(bool $paramTagsHaveTypes): void
  {
    $this->paramTagsHaveTypes = $paramTagsHaveTypes;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
