<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader;

use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Common\Loader\Helper\LoaderContext;

/**
 * Class for loading a single stored routine into a RDBMS instance given an SQL file.
 */
abstract class CommonRoutineLoader
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output object.
   *
   * @var StratumStyle
   */
  protected StratumStyle $io;

  /**
   * The actual routine source code.
   *
   * @var string
   */
  protected string $routineSourceCode;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param StratumStyle $io The output object.
   */
  public function __construct(StratumStyle $io)
  {
    $this->io = $io;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the stored routine into the RDBMS instance.
   *
   * @param LoaderContext $context The loader context.
   */
  public function loadStoredRoutine(LoaderContext $context): ?bool
  {
    try
    {
      if ($this->mustReload($context))
      {
        $this->extractName($context);

        $this->io->text(sprintf('Loading %s <dbo>%s</dbo>',
                                $context->storedRoutine->getType(),
                                $context->storedRoutine->getName()));

        $this->updateSourceTypeHints($context, $this->io);
        $this->substitutePlaceholders($context);
        $this->loadRoutineFile($context);
        $this->extractInsertMultipleTableColumns($context);
        $this->extractStoredRoutineParameters($context);
        $this->validateParameterLists($context);
        $this->updateMetadata($context);

        return true;
      }

      return null;
    }
    catch (\Throwable $exception)
    {
      $this->logException($exception);
      if ($this->io->isVerbose())
      {
        $e = $exception;
        do
        {
          $lines = [sprintf('#0 %s(%d):', $e->getFile(), $e->getLine())];
          foreach ($e->getTrace() as $index => $trace)
          {
            $lines[] = sprintf('#%d %s(%d): %s', $index + 2, $trace['file'], $trace['line'], $trace['class'] ?? '');
          }
          $this->io->writeln($lines);
          $this->io->writeln('');
        } while ($e = $e->getPrevious());
      }

      return false;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the column names and column types of the current table for insert multiple.
   *
   * @param LoaderContext $context The loader context.
   */
  abstract protected function extractInsertMultipleTableColumns(LoaderContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the name of the stored routine and the stored routine type (i.e., procedure or function) source.
   *
   * @param LoaderContext $context The loader context.
   */
  abstract protected function extractName(LoaderContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the metadata of the stored routine parameters from the RDBMS.
   *
   * @param LoaderContext $context The loader context.
   */
  abstract protected function extractStoredRoutineParameters(LoaderContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the DocBlock parts to be used by the wrapper generator.
   *
   * @param LoaderContext $context The loader context.
   */
  protected function getPhpDoc(LoaderContext $context): array
  {
    $phpDoc = ['description' => $context->docBlock->getDescription()];

    $parameters = [];
    foreach ($context->storedRoutine->getParameters() as $parameter)
    {
      $parameters[] = ['data_type'      => $parameter['data_type'],
                       'dtd_identifier' => $parameter['dtd_identifier'],
                       'description'    => $context->docBlock->getParameterDescription($parameter['parameter_name']),
                       'name'           => $parameter['parameter_name'],
                       'php_type'       => $context->dataType->columnTypeToPhpType($parameter).'|null'];
    }

    $phpDoc['parameters'] = $parameters;

    $designation = $context->docBlock->getDesignation();
    if (isset($designation['return']))
    {
      $returnTypes = $designation['return'];
      if (in_array('*', $returnTypes))
      {
        $phpDoc['return'] = ['mixed'];
      }
      elseif (sizeof($returnTypes)===1 && $returnTypes[0]==='bool')
      {
        $phpDoc['return'] = ['bool'];
      }
      else
      {
        $phpDoc['return'] = [];
        $nullable         = false;
        foreach ($returnTypes as $returnType)
        {
          if ($returnType==='null')
          {
            $nullable = true;
          }
          else
          {
            $phpDoc['return'][] = $context->dataType->columnTypeToPhpType(['data_type' => $returnType]);
          }

          $phpDoc['return'] = array_unique($phpDoc['return']);
          sort($phpDoc['return']);
          if ($nullable)
          {
            $phpDoc['return'][] = 'null';
          }
        }
      }
    }

    return $phpDoc;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the stored routine into the RDBMS instance.
   *
   * @param LoaderContext $context The loader context.
   */
  abstract protected function loadRoutineFile(LoaderContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Logs an exception.
   *
   * @param \Throwable $exception The exception.
   */
  protected function logException(\Throwable $exception): void
  {
    $this->io->error(trim($exception->getMessage()));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether the source file must be load or reloaded.
   *
   * @param LoaderContext $context The build context.
   *
   * @return bool
   */
  protected function mustReload(LoaderContext $context): bool
  {
    if (empty($context->oldPhpStratumMetadata))
    {
      return true;
    }

    if ($context->oldPhpStratumMetadata['timestamp']!==$context->storedRoutine->getMtime())
    {
      return true;
    }

    if (!empty($context->oldPhpStratumMetadata['type_hints']))
    {
      if (!$context->typeHints->compareTypeHints($context->oldPhpStratumMetadata['type_hints']))
      {
        return true;
      }
    }

    if (!empty($context->oldPhpStratumMetadata['placeholders']))
    {
      if (!$context->placeHolders->comparePlaceholders($context->oldPhpStratumMetadata['placeholders']))
      {
        return true;
      }
    }

    if (empty($context->oldRdbmsMetadata))
    {
      return true;
    }

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds magic constants to the placeholders.
   *
   * @param LoaderContext $context The loader context.
   */
  protected function setMagicConstants(LoaderContext $context): void
  {
    $realPath    = realpath($context->storedRoutine->getPath());
    $pathInfo    = pathinfo($realPath);
    $routineName = $pathInfo['filename'];
    $dirName     = $pathInfo['dirname'];

    $context->placeHolders->addPlaceholder('__FILE__', $realPath, true);
    $context->placeHolders->addPlaceholder('__ROUTINE__', $routineName, true);
    $context->placeHolders->addPlaceholder('__DIR__', $dirName, true);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Substitutes all placeholders with their actual values in the source of the routine.
   *
   * @param LoaderContext $context The loader context.
   */
  protected function substitutePlaceholders(LoaderContext $context): void
  {
    $this->setMagicConstants($context);
    $this->routineSourceCode = $context->placeHolders->substitutePlaceholders($context->storedRoutine->getCodeLines());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the metadata of the stored routine.
   *
   * @param LoaderContext $context The loader context.
   */
  protected function updateMetadata(LoaderContext $context): void
  {
    $placeholders = $context->placeHolders->extractPlaceHolders($context->storedRoutine);
    $typeHints    = $context->typeHints->extractTypeHints($context->storedRoutine);

    $context->newPhpStratumMetadata['designation']  = $context->docBlock->getDesignation();
    $context->newPhpStratumMetadata['parameters']   = $context->storedRoutine->getParameters();
    $context->newPhpStratumMetadata['php_doc']      = $this->getPhpDoc($context);
    $context->newPhpStratumMetadata['placeholders'] = $placeholders;
    $context->newPhpStratumMetadata['routine_name'] = $context->storedRoutine->getName();
    $context->newPhpStratumMetadata['timestamp']    = $context->storedRoutine->getMtime();
    $context->newPhpStratumMetadata['type_hints']   = $typeHints;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the source of the stored routine based on the values of type hints.
   *
   * @param LoaderContext $context The loader context.
   * @param StratumStyle  $io      The output decorator.
   */
  protected function updateSourceTypeHints(LoaderContext $context, StratumStyle $io): void
  {
    $lines = $context->typeHints->updateTypes($context->storedRoutine->getCodeLines(), $context->dataType);
    $lines = $context->typeHints->alignTypeHints($lines);

    $context->storedRoutine->updateSource(implode(PHP_EOL, $lines), $io);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates the parameters found the DocBlock in the source of the stored routine against the parameters from the
   * metadata of RDBMS and reports missing and unknown parameters names.
   *
   * @param LoaderContext $context The loader context.
   */
  protected function validateParameterLists(LoaderContext $context): void
  {
    // Make list with names of parameters used in database.
    $databaseParametersNames = [];
    foreach ($context->storedRoutine->getParameters() as $parameter)
    {
      $databaseParametersNames[] = $parameter['parameter_name'];
    }

    // Make list with names of parameters used in dock block of routine.
    $docBlockParametersNames = [];
    foreach ($context->docBlock->getParameters() as $parameter)
    {
      $docBlockParametersNames[] = $parameter['name'];
    }

    // Check and show warning if any parameters is missing in doc block.
    $tmp = array_diff($databaseParametersNames, $docBlockParametersNames);
    foreach ($tmp as $name)
    {
      $this->io->logNote('Parameter <dbo>%s</dbo> is missing from doc block', $name);
    }

    // Checks and shows a warning if fond unknown parameters in doc block.
    $tmp = array_diff($docBlockParametersNames, $databaseParametersNames);
    foreach ($tmp as $name)
    {
      $this->io->logNote('Unknown parameter <dbo>%s</dbo> found in doc block', $name);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
