<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Backend;

use SetBased\Exception\RuntimeException;
use SetBased\Helper\CodeStore\PhpCodeStore;
use SetBased\Stratum\Backend\Config;
use SetBased\Stratum\Backend\RoutineWrapperGeneratorWorker;
use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;
use SetBased\Stratum\Common\Helper\Util;
use SetBased\Stratum\Common\Wrapper\Helper\WrapperContext;
use SetBased\Stratum\Middle\NameMangler\NameMangler;

/**
 * Abstract command for generating a class with wrapper methods for invoking stored routines in a MySQL or MariaDB
 * database.
 */
abstract class CommonRoutineWrapperGeneratorWorker implements RoutineWrapperGeneratorWorker
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The settings from the PhpStratum configuration file.
   *
   * @var Config
   */
  protected Config $config;

  /**
   * Array with fully qualified names that must be imported.
   *
   * @var array
   */
  protected array $imports = [];

  /**
   * The output object.
   *
   * @var StratumStyle
   */
  protected StratumStyle $io;

  /**
   * Store php code with indention.
   *
   * @var PhpCodeStore
   */
  private PhpCodeStore $codeStore;

  /**
   * Class name for mangling routine and parameter names.
   *
   * @var string
   */
  private string $nameManglerClassName;

  /**
   * The class name (including namespace) of the parent class of the routine wrapper.
   *
   * @var string
   */
  private string $parentClassName;

  /**
   * The filename of the file with the metadata of all stored procedures.
   *
   * @var string
   */
  private string $phpStratumMetadataPath;

  /**
   * If true wrapper must declare strict types.
   *
   * @var bool
   */
  private bool $strictTypes;

  /**
   * The class name (including namespace) of the routine wrapper.
   *
   * @var string|null
   */
  private ?string $wrapperClassName;

  /**
   * The filename where the generated wrapper class must be stored
   *
   * @var string
   */
  private string $wrapperFilename;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param Config       $settings The settings from the PhpStratum configuration file.
   * @param StratumStyle $io       The output object.
   */
  public function __construct(Config $settings, StratumStyle $io)
  {
    $this->config = $settings;
    $this->io     = $io;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   *
   * @throws RuntimeException
   */
  public function execute(): int
  {
    $this->readConfigurationFile();

    if ($this->wrapperClassName!==null)
    {
      $this->io->title('PhpStratum: Wrapper');

      $this->generateWrapperClass();
    }

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Builds a complete wrapper method for invoking a stored routine.
   *
   * @param WrapperContext $context The wrapper context.
   *
   */
  abstract protected function buildRoutineWrapper(WrapperContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a data type helper object appropriate for the RDBMS.
   */
  abstract protected function createDataTypeHelper(): CommonDataTypeHelper;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a class header for the stored routine wrappers.
   */
  private function generateClassHeader(): void
  {
    $p = strrpos($this->wrapperClassName, '\\');
    if ($p!==false)
    {
      $namespace = ltrim(substr($this->wrapperClassName, 0, $p), '\\');
      $className = substr($this->wrapperClassName, $p + 1);
    }
    else
    {
      $namespace = null;
      $className = $this->wrapperClassName;
    }

    // Write PHP tag.
    $this->codeStore->append('<?php');

    // Write strict types.
    if ($this->strictTypes)
    {
      $this->codeStore->append('declare(strict_types=1);');
    }

    // Write name space of the wrapper class.
    if ($namespace!==null)
    {
      $this->codeStore->append('');
      $this->codeStore->append(sprintf('namespace %s;', $namespace));
      $this->codeStore->append('');
    }

    // If the child class and parent class have different names import the parent class. Otherwise, use the fully
    // qualified parent class name.
    $parentClassName = substr($this->parentClassName, strrpos($this->parentClassName, '\\') + 1);
    if ($className!==$parentClassName)
    {
      $this->imports[]       = $this->parentClassName;
      $this->parentClassName = $parentClassName;
    }

    // Write use statements.
    if (!empty($this->imports))
    {
      $this->imports = array_unique($this->imports, SORT_REGULAR);
      sort($this->imports);
      foreach ($this->imports as $import)
      {
        $this->codeStore->append(sprintf('use %s;', $import));
      }
      $this->codeStore->append('');
    }

    // Write class name.
    $this->codeStore->append('/**');
    $this->codeStore->append(' * The data layer.', false);
    $this->codeStore->append(' */', false);
    $this->codeStore->append(sprintf('class %s extends %s', $className, $this->parentClassName));
    $this->codeStore->append('{');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a class trailer for the stored routine wrappers.
   */
  private function generateClassTrailer(): void
  {
    $this->codeStore->appendSeparator();
    $this->codeStore->append('}');
    $this->codeStore->append('');
    $this->codeStore->appendSeparator();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the wrapper class.
   *
   * @throws RuntimeException
   */
  private function generateWrapperClass(): void
  {
    $dataTypeHelper  = $this->createDataTypeHelper();
    $this->codeStore = new PhpCodeStore();

    /** @var NameMangler $mangler */
    $mangler            = new $this->nameManglerClassName();
    $phpStratumMetadata = $this->readPhpStratumMetadata();

    // Sort routines by their wrapper method name.
    $sortedRoutines = [];
    foreach ($phpStratumMetadata as $routine)
    {
      $methodName                  = $mangler::getMethodName($routine['routine_name']);
      $sortedRoutines[$methodName] = $routine;
    }
    ksort($sortedRoutines);

    foreach ($sortedRoutines as $routine)
    {
      if ($routine['designation']['type']!=='hidden')
      {
        $context = new WrapperContext($dataTypeHelper, $this->codeStore, $mangler, $routine);
        $this->buildRoutineWrapper($context);
      }
    }

    $wrappers        = $this->codeStore->getRawCode();
    $this->codeStore = new PhpCodeStore();
    $this->generateClassHeader();
    $this->codeStore->append($wrappers, false);
    $this->generateClassTrailer();
    Util::writeTwoPhases($this->wrapperFilename, $this->codeStore->getCode(), $this->io);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file.
   */
  private function readConfigurationFile(): void
  {
    $this->wrapperClassName = $this->config->optString('wrapper.wrapper_class');
    if ($this->wrapperClassName!==null)
    {
      $this->parentClassName        = $this->config->manString('wrapper.parent_class');
      $this->nameManglerClassName   = $this->config->manString('wrapper.mangler_class');
      $this->wrapperFilename        = $this->config->manString('wrapper.wrapper_file');
      $this->phpStratumMetadataPath = $this->config->manString('loader.metadata');
      $this->strictTypes            = $this->config->manBool('wrapper.strict_types', true);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of stored routines.
   *
   * @return array
   */
  private function readPhpStratumMetadata(): array
  {
    try
    {
      $metadata = json_decode(file_get_contents($this->phpStratumMetadataPath), true, flags: JSON_THROW_ON_ERROR);
      $metadata = $metadata['stored_routines'];
    }
    catch (\Throwable)
    {
      $metadata = [];
    }

    return $metadata;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
