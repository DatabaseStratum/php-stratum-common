<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Backend;

use SetBased\Stratum\Backend\Config;
use SetBased\Stratum\Backend\RoutineLoaderWorker;
use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;
use SetBased\Stratum\Common\Helper\Util;
use SetBased\Stratum\Common\Loader\CommonRoutineLoader;
use SetBased\Stratum\Common\Loader\Helper\DocBlockHelper;
use SetBased\Stratum\Common\Loader\Helper\EscapeHelper;
use SetBased\Stratum\Common\Loader\Helper\LoaderContext;
use SetBased\Stratum\Common\Loader\Helper\PlaceholderHelper;
use SetBased\Stratum\Common\Loader\Helper\SourceFinderHelper;
use SetBased\Stratum\Common\Loader\Helper\StoredRoutineHelper;
use SetBased\Stratum\Common\Loader\Helper\TypeHintHelper;
use SetBased\Stratum\Middle\NameMangler\NameMangler;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Abstract command for loading stored routines.
 */
abstract class CommonRoutineLoaderWorker implements RoutineLoaderWorker
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The settings from the PhpStratum configuration file.
   *
   * @var Config
   */
  protected Config $config;

  /**
   * The output object.
   *
   * @var StratumStyle
   */
  protected StratumStyle $io;

  /**
   * Name of the class that contains all constants.
   *
   * @var string|null
   */
  private ?string $constantClassName;

  /**
   * An array with source filenames that are not loaded into the RDBMS instance.
   *
   * @var string[]
   */
  private array $errorFilenames = [];

  /**
   * Class name for mangling routine and parameter names.
   *
   * @var string|null
   */
  private ?string $nameManglerClassName = null;

  /**
   * The metadata of all stored routines.
   *
   * @var array
   */
  private array $phpStratumMetadata;

  /**
   * The filename of the file with the metadata of all stored routines.
   *
   * @var string
   */
  private string $phpStratumMetadataPath;

  /**
   * A map from placeholders that are actually used in the stored routine to their values.
   *
   * @var PlaceholderHelper|null
   */
  private ?PlaceholderHelper $placeholders = null;

  /**
   * The metadata about all stored routines currently in the RDBMS instance.
   *
   * @var array
   */
  private array $rdbmsMetadata = [];

  /**
   * All found source files.
   *
   * @var array
   */
  private array $sourceFilenames = [];

  /**
   * The patterns for finding the sources of stored routines.
   *
   * @var string
   */
  private string $sources;

  /**
   * A map from type hints to their actual data types.
   *
   * @var TypeHintHelper
   */
  private TypeHintHelper $typeHints;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param Config       $settings The settings from the PhpStratum configuration file.
   * @param StratumStyle $io       The output object.
   */
  public function __construct(Config $settings, StratumStyle $io)
  {
    $this->config    = $settings;
    $this->io        = $io;
    $this->typeHints = new TypeHintHelper();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function execute(?array $sources = null): int
  {
    $this->io->title('PhpStratum: Loader');

    $this->readConfigFile();

    if (empty($sources))
    {
      $this->loadAll();
    }
    else
    {
      $this->loadList($sources);
    }

    $this->logOverviewErrors();

    return ($this->errorFilenames) ? 1 : 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a replacement to the map of replacement pairs.
   *
   * @param string $name  The name of the replacement pair.
   * @param mixed  $value The value of the replacement pair.
   *
   * @return void
   */
  protected function addPlaceholder(string $name, mixed $value): void
  {
    $placeholders = $this->getPlaceholders();

    $type = gettype($value);
    switch ($type)
    {
      case 'boolean':
        $placeholders->addPlaceholder($name, ($value) ? '1' : '0', false);
        break;

      case 'integer':
      case 'double':
        $placeholders->addPlaceholder($name, (string)$value, false);
        break;

      case 'string':
        $placeholders->addPlaceholder($name, $value, true);
        break;

      default:
        $this->io->logVerbose("Ignoring constant %s which is an instance of %s.", $name, $type);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a replacement to the map of replacement pairs.
   *
   * @param string $typeHint The type hint.
   * @param string $datatype The actual data type.
   *
   * @return void
   */
  protected function addTypeHint(string $typeHint, string $datatype): void
  {
    $this->typeHints->addTypeHint($typeHint, $datatype);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the RDBMS instance.
   */
  abstract protected function connect(): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a data type helper object appropriate for the RDBMS.
   */
  abstract protected function createDataTypeHelper(): CommonDataTypeHelper;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an object for escaping string such that they are to use in SQL code.
   *
   * @return EscapeHelper
   */
  abstract protected function createEscaper(): EscapeHelper;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates a Routine Loader object.
   *
   * @param LoaderContext $context The loader context.
   *
   * @return CommonRoutineLoader
   */
  abstract protected function createRoutineLoader(LoaderContext $context): CommonRoutineLoader;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the RDBMS instance.
   */
  abstract protected function disconnect(): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops a stored routine.
   *
   * @param array $rdbmsMetadata The metadata from the RDBMS of the stored routine to be dropped.
   */
  abstract protected function dropStoredRoutine(array $rdbmsMetadata): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects schema, table, column names and the column type from the RDBMS instance and saves them as placeholders.
   */
  abstract protected function fetchColumnTypes(): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves metadata about all stored routines currently in the current schema of the RDBMS.
   */
  abstract protected function fetchRdbmsMetadata(): array;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes RDBMS specific initializations.
   */
  protected function initRdbmsSpecific(): void
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the revision of the format of the metadata of the stored routines.
   */
  protected function phpStratumMetadataRevision(): string
  {
    return '4';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file.
   */
  protected function readConfigFile(): void
  {
    $this->sources                = $this->config->manString('loader.sources');
    $this->phpStratumMetadataPath = $this->config->manString('loader.metadata');
    $this->constantClassName      = $this->config->optString('constants.class');
    $this->nameManglerClassName   = $this->config->optString('wrapper.mangler_class');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Detects stored routines that would result in duplicate wrapper method name.
   */
  private function detectNameConflicts(): void
  {
    // Get same method names from array
    [$sourcesByPath, $sourcesByMethod] = $this->getDuplicates();

    // Add every not unique method name to myErrorFileNames
    foreach ($sourcesByPath as $source)
    {
      $this->errorFilenames[] = $source['path_name'];
    }

    // Log the sources files with duplicate method names.
    foreach ($sourcesByMethod as $method => $sources)
    {
      $tmp = [];
      foreach ($sources as $source)
      {
        $tmp[] = $source['path_name'];
      }

      $this->io->error(sprintf("The following source files would result wrapper methods with equal name '%s'",
                               $method));
      $this->io->listing($tmp);
    }

    // Remove duplicates from sources.
    foreach ($this->sourceFilenames as $key => $source)
    {
      if (isset($sourcesByPath[$source['path_name']]))
      {
        unset($this->sourceFilenames[$key]);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops obsolete stored routines (i.e., stored routines that exit in the current schema but for which we don't have a
   * source file).
   */
  private function dropObsoleteRoutines(): void
  {
    $inSources = [];
    foreach ($this->sourceFilenames as $source)
    {
      $inSources[$source['routine_name']] = $source;
    }

    $obsolete = array_diff_key($this->rdbmsMetadata, $inSources);
    foreach ($obsolete as $routineName => $rdbmsMetadata)
    {
      $this->io->text(sprintf('Dropping %s <dbo>%s</dbo>.',
                              mb_strtolower($rdbmsMetadata['routine_type']),
                              $routineName));

      $this->dropStoredRoutine($rdbmsMetadata);
      unset($this->phpStratumMetadata[$routineName]);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the constants from the class that acts like a namespace for constants and adds them to the replacement pairs.
   */
  private function fetchConstants(): void
  {
    if ($this->constantClassName!==null)
    {
      $reflection = new \ReflectionClass($this->constantClassName);
      $constants  = $reflection->getConstants();
      foreach ($constants as $name => $value)
      {
        $this->addPlaceholder('@'.$name.'@', $value);
      }

      $this->io->text(sprintf('Read %d constants for substitution from <fso>%s</fso>',
                              sizeof($constants),
                              OutputFormatter::escape(Util::relativeRealPath($reflection->getFileName()))));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Searches recursively for all source files.
   */
  private function findSourceFiles(): void
  {
    $helper    = new SourceFinderHelper(dirname($this->config->manString('stratum.config_path')));
    $filenames = $helper->findSources($this->sources);
    foreach ($filenames as $filename)
    {
      $routineName             = pathinfo($filename, PATHINFO_FILENAME);
      $this->sourceFilenames[] = ['path_name'    => $filename,
                                  'routine_name' => $routineName,
                                  'method_name'  => $this->methodName($routineName)];
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Finds all source files that actually exists from a list of file names.
   *
   * @param string[] $filenames The list of file names.
   */
  private function findSourceFilesFromList(array $filenames): void
  {
    foreach ($filenames as $path)
    {
      if (!file_exists($path))
      {
        $this->io->error(sprintf("File not exists: '%s'", $path));
        $this->errorFilenames[] = $path;
      }
      else
      {
        $routineName             = pathinfo($path, PATHINFO_FILENAME);
        $this->sourceFilenames[] = ['path_name'    => $path,
                                    'routine_name' => $routineName,
                                    'method_name'  => $this->methodName($routineName)];
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns all elements in {@link $sources} with duplicate method names.
   *
   * @return array[]
   */
  private function getDuplicates(): array
  {
    // First pass make lookup table by method_name.
    $lookup = [];
    foreach ($this->sourceFilenames as $source)
    {
      if (isset($source['method_name']))
      {
        if (!isset($lookup[$source['method_name']]))
        {
          $lookup[$source['method_name']] = [];
        }

        $lookup[$source['method_name']][] = $source;
      }
    }

    // Second pass find duplicate sources.
    $duplicatesSources = [];
    $duplicatesMethods = [];
    foreach ($this->sourceFilenames as $source)
    {
      if (sizeof($lookup[$source['method_name']])>1)
      {
        $duplicatesSources[$source['path_name']]   = $source;
        $duplicatesMethods[$source['method_name']] = $lookup[$source['method_name']];
      }
    }

    return [$duplicatesSources, $duplicatesMethods];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the map from placeholders that are actually used in the stored routine to their values.
   *
   * @return PlaceholderHelper
   */
  private function getPlaceholders(): PlaceholderHelper
  {
    if ($this->placeholders===null)
    {
      $this->placeholders = new PlaceholderHelper($this->createEscaper());
    }

    return $this->placeholders;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the metadata of all stored routines currently the RDBMS instance.
   */
  private function initRdbmsStoredRoutineMetadata(): void
  {
    $rows = $this->fetchRdbmsMetadata();
    foreach ($rows as $row)
    {
      $this->rdbmsMetadata[$row['routine_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines into MySQL.
   */
  private function loadAll(): void
  {
    $this->connect();
    $this->findSourceFiles();
    $this->detectNameConflicts();
    $this->fetchColumnTypes();
    $this->fetchConstants();
    $this->readPhpStratumMetadata();
    $this->initRdbmsStoredRoutineMetadata();
    $this->initRdbmsSpecific();
    $this->loadStoredRoutines();
    $this->dropObsoleteRoutines();
    $this->writePhpStratumMetadata();
    $this->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines in a list into MySQL.
   *
   * @param string[] $filenames The list of files to be loaded.
   */
  private function loadList(array $filenames): void
  {
    $this->connect();
    $this->findSourceFilesFromList($filenames);
    $this->detectNameConflicts();
    $this->fetchColumnTypes();
    $this->fetchConstants();
    $this->readPhpStratumMetadata();
    $this->initRdbmsStoredRoutineMetadata();
    $this->initRdbmsSpecific();
    $this->loadStoredRoutines();
    $this->writePhpStratumMetadata();
    $this->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines.
   */
  private function loadStoredRoutines(): void
  {
    ksort($this->sourceFilenames);

    $dataTypeHelper = $this->createDataTypeHelper();
    foreach ($this->sourceFilenames as $source)
    {
      $routineSource         = new StoredRoutineHelper($source['path_name']);
      $docBlock              = new DocBlockHelper($routineSource);
      $oldRdbmsMetadata      = $this->rdbmsMetadata[$source['routine_name']] ?? [];
      $oldPhpStratumMetadata = $this->phpStratumMetadata[$source['routine_name']] ?? [];
      $newPhpStratumMetadata = [];

      $context = new LoaderContext(dataType: $dataTypeHelper,
        storedRoutine:                       $routineSource,
        typeHints:                           $this->typeHints,
        docBlock:                            $docBlock,
        placeHolders:                        $this->getPlaceholders(),
        oldRdbmsMetadata:                    $oldRdbmsMetadata,
        oldPhpStratumMetadata:               $oldPhpStratumMetadata,
        newPhpStratumMetadata:               $newPhpStratumMetadata);

      $routineLoader = $this->createRoutineLoader($context);
      $success       = $routineLoader->loadStoredRoutine($context);

      if ($success===false)
      {
        $this->errorFilenames[] = $source['path_name'];
        unset($this->phpStratumMetadata[$source['routine_name']]);
      }
      elseif ($success===true)
      {
        $this->phpStratumMetadata[$source['routine_name']] = $context->newPhpStratumMetadata;
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Logs the source files that were not successfully loaded into MySQL.
   */
  private function logOverviewErrors(): void
  {
    if (!empty($this->errorFilenames))
    {
      $this->io->warning('Routines in the files below are not loaded:');
      $this->io->listing($this->errorFilenames);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the method name in the wrapper for a stored routine. Returns null when name mangler is not set.
   *
   * @param string $routineName The name of the routine.
   *
   * @return string|null
   */
  private function methodName(string $routineName): ?string
  {
    if ($this->nameManglerClassName!==null)
    {
      /** @var NameMangler $mangler */
      $mangler = $this->nameManglerClassName;

      return $mangler::getMethodName($routineName);
    }

    return null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the metadata of the stored routines.
   */
  private function readPhpStratumMetadata(): void
  {
    try
    {
      $metadata = json_decode(file_get_contents($this->phpStratumMetadataPath), true, flags: JSON_THROW_ON_ERROR);
      if ($metadata['php_stratum_metadata_revision']===$this->phpStratumMetadataRevision())
      {
        $this->phpStratumMetadata = $metadata['stored_routines'];
      }
      else
      {
        $this->phpStratumMetadata = [];
      }
    }
    catch (\Throwable)
    {
      $this->phpStratumMetadata = [];
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes the metadata of all stored routines to the metadata file.
   */
  private function writePhpStratumMetadata(): void
  {
    /**
     * Recursively sorts a multidimensional array on its keys.
     *
     * @param array $array The array.
     */
    function recursiveKeySort(array &$array): void
    {
      foreach ($array as $value)
      {
        if (is_array($value))
        {
          recursiveKeySort($value);
        }
      }
      ksort($array);
    }

    $metadata = ['php_stratum_metadata_revision' => $this->phpStratumMetadataRevision(),
                 'stored_routines'               => $this->phpStratumMetadata];
    recursiveKeySort($metadata);
    file_put_contents($this->phpStratumMetadataPath,
                      json_encode($metadata, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
