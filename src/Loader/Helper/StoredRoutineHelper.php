<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

use SetBased\Exception\LogicException;
use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Common\Exception\RoutineLoaderException;
use SetBased\Stratum\Common\Helper\Util;

/**
 * Helper class for the source and metadata of a stored routine.
 *
 * @property-read $code
 */
class StoredRoutineHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The code of the stored routine.
   *
   * @var string|null
   */
  private ?string $code = null;

  /**
   * The code of the stored routine as an array of strings.
   *
   * @var array|null
   */
  private ?array $codeLines = null;

  /**
   * The last modification time of the source file.
   *
   * @var int|null
   */
  private ?int $mtime = null;

  /**
   * The name of the stored routine.
   *
   * @var string|null
   */
  private ?string $name = null;

  /**
   * The metadata of the parameters of the stored routine.
   *
   * @var array
   */
  private array $parameters;

  /**
   * The path to the source of the stored routine.
   *
   * @var string
   */
  private string $path;

  /**
   * The type (procedure or function) of the stored routine.
   *
   * @var string|null
   */
  private ?string $type = null;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $path The path to the source of the stored routine.
   */
  public function __construct(string $path)
  {
    $this->path = $path;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The code of the stored routine.
   *
   * @return string
   */
  public function getCode(): string
  {
    if ($this->code===null)
    {
      $this->readSourceFile();
    }

    return $this->code;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the code of the stored routine as an array of strings.
   *
   * @return array
   */
  public function getCodeLines(): array
  {
    if ($this->codeLines===null)
    {
      $this->readSourceFile();
    }

    return $this->codeLines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return the last modification time of the source file.
   *
   * @return int
   */
  public function getMtime(): int
  {
    if ($this->mtime===null)
    {
      $this->readSourceFile();
    }

    return $this->mtime;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the stored routine.
   */
  public function getName(): string
  {
    return $this->name;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of the parameters of the stored routine.
   */
  public function getParameters(): array
  {
    return $this->parameters;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the path to the source of the stored routine.
   *
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type of the stored routine.
   *
   * @return string
   */
  public function getType(): string
  {
    if ($this->type===null)
    {
      throw new RoutineLoaderException('Type has not been set yet.');
    }

    return $this->type;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the name of the stored routine as found in the source code of the stored routine.
   *
   * @param string $name The name of the stored routine.
   */
  public function setName(string $name): void
  {
    if ($this->name!==null)
    {
      throw new LogicException('Name has been set already.');
    }

    $basename = pathinfo($this->path, PATHINFO_FILENAME);
    if ($basename!==$name)
    {
      throw new RoutineLoaderException("Stored routine name '%s' does not correspond with the basename of '%s'.",
                                       $name,
                                       Util::relativeRealPath($this->path));
    }

    $this->name = $name;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the metadata of the parameters of the stored routine.
   *
   * @param array $parameters The metadata of the parameters.
   */
  public function setParameters(array $parameters): void
  {
    $this->parameters = $parameters;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the type of the stored routine.
   *
   * @param string $type The type of the stored routine.
   */
  public function setType(string $type): void
  {
    if ($this->type!==null)
    {
      throw new LogicException('Type has been set already.');
    }

    $this->type = $type;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the source of the stored routine if the new code is different from the current code.
   *
   * @param string       $code The new code.
   * @param StratumStyle $io   The output decorator.
   */
  public function updateSource(string $code, StratumStyle $io): void
  {
    if ($this->code!==$code)
    {
      Util::writeTwoPhases($this->path, $code, $io);
      $this->readSourceFile();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the file with the source of the stored routine.
   *
   * @return void
   */
  private function readSourceFile(): void
  {
    try
    {
      $this->code      = file_get_contents($this->path);
      $this->codeLines = explode("\n", $this->code);
      $this->mtime     = filemtime($this->path);
    }
    catch (\Exception $exception)
    {
      throw new RoutineLoaderException([$exception], "Unable to read '%s'.", $this->path);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
