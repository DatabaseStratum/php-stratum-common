<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Wrapper;

use SetBased\Helper\CodeStore\PhpCodeStore;
use SetBased\Stratum\Common\Wrapper\Helper\WrapperContext;

/**
 * Abstract parent class for all wrapper generators.
 */
abstract class CommonWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Array with fully qualified names that must be imported for this wrapper method.
   *
   * @var array
   */
  protected array $imports = [];

  /**
   * The exceptions that the wrapper can throw.
   *
   * @var string[]
   */
  private array $throws;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param WrapperContext $context The wrapper context.
   */
  abstract public static function createRoutineWrapper(WrapperContext $context): CommonWrapper;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a wrapper method for a stored routine.
   *
   * @param WrapperContext $context The wrapper context.
   */
  final public function generateMethod(WrapperContext $context): void
  {
    [$context->codeStore, $tmp] = [new PhpCodeStore(), $context->codeStore];

    $context->codeStore->append(sprintf('public function %s(%s)%s',
                                        $context->mangler::getMethodName($context->phpStratumMetadata['routine_name']),
                                        $this->getWrapperArgs($context),
                                        $this->getReturnTypeDeclaration($context)));
    $context->codeStore->append('{');
    $this->generateMethodBody($context);
    $context->codeStore->append('}');
    $context->codeStore->append('');

    [$context->codeStore, $tmp] = [$tmp, $context->codeStore];

    $context->codeStore->appendSeparator();
    $this->generatePhpDocBlock($context);
    $context->codeStore->append($tmp->getRawCode(), false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an array with fully qualified names that must be imported in the stored routine wrapper class.
   *
   * @return array
   */
  final public function getImports(): array
  {
    return $this->imports;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a throw tag to the odc block of the generated method.
   *
   * @param string $class The name of the exception.
   */
  final public function throws(string $class): void
  {
    $parts                = explode('\\', $class);
    $this->throws[$class] = array_pop($parts);
    $this->imports[]      = $class;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Enhances the metadata of the parameters of the store routine wrapper.
   *
   * @param array[] $parameters The metadata of the parameters. For each parameter the following keys must be defined:
   *                            <ul>
   *                            <li> description    The description of the parameter.
   *                            <li> dtd_identifier The data type of the corresponding parameter of
   *                            the stored routine. Null if there is no corresponding parameter.
   *                            <li> php_name       The name of the parameter (including $).
   *                            <li> php_type       The type of the parameter.
   *                            </ul>
   */
  protected function enhancePhpDocBlockParameters(array &$parameters): void
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param WrapperContext $context
   */
  abstract protected function generateMethodBody(WrapperContext $context): void;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the return type to be used in the DocBlock.
   *
   * @param WrapperContext $context The wrapper context.
   */
  abstract protected function getDocBlockReturnType(WrapperContext $context): string;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the return type declaration of the wrapper method.
   *
   * @param WrapperContext $context The wrapper context.
   */
  abstract protected function getReturnTypeDeclaration(WrapperContext $context): string;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the parameters of the wrapper method for the stored routine.
   *
   * @param WrapperContext $context The wrapper context.
   */
  protected function getWrapperArgs(WrapperContext $context): string
  {
    $parameterList = [];

    if ($context->phpStratumMetadata['designation']['type']==='bulk')
    {
      $parameterList[] = 'BulkHandler $bulkHandler';
    }

    foreach ($context->phpStratumMetadata['php_doc']['parameters'] as $parameter)
    {
      $phpParameter = '$'.$context->mangler::getParameterName($parameter['name']);
      $phpType      = $parameter['php_type'];
      $types        = explode('|', $phpType);
      if (sizeof($types)===0)
      {
        $parameterList[] = $phpParameter;
      }
      elseif (sizeof($types)===2 && $types[1]==='null')
      {
        $parameterList[] = sprintf('?%s %s', $types[0], $phpParameter);
      }
      else
      {
        $parameterList[] = sprintf('%s %s', $phpType, $phpParameter);
      }
    }

    return implode(', ', $parameterList);
  }


  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether the routine has arguments.
   *
   * @param WrapperContext $context The wrapper context.
   */
  protected function hasRoutineArgs(WrapperContext $context): bool
  {
    return !empty($context->phpStratumMetadata['php_doc']['parameters']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate php doc block in the data layer for stored routine.
   *
   * @param WrapperContext $context The wrapper context.
   */
  private function generatePhpDocBlock(WrapperContext $context): void
  {
    $context->codeStore->append('/**', false);

    $this->generatePhpDocBlockDescription($context);
    $this->generatePhpDocBlockParameters($context);
    $this->generatePhpDocBlockReturn($context);
    $this->generatePhpDocBlockThrow($context);

    $context->codeStore->append(' */', false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the description of stored routine wrapper.
   *
   * @param WrapperContext $context The wrapper context.
   */
  private function generatePhpDocBlockDescription(WrapperContext $context): void
  {
    if (!empty($context->phpStratumMetadata['php_doc']['description']))
    {
      foreach ($context->phpStratumMetadata['php_doc']['description'] as $line)
      {
        $context->codeStore->append(' * '.$line, false);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the doc block for parameters of stored routine wrapper.
   *
   * @param WrapperContext $context The wrapper context.
   */
  private function generatePhpDocBlockParameters(WrapperContext $context): void
  {
    $parameters = [];
    foreach ($context->phpStratumMetadata['php_doc']['parameters'] as $parameter)
    {
      $mangledName = $context->mangler::getParameterName($parameter['name']);

      $parameters[] = ['php_name'       => '$'.$mangledName,
                       'description'    => $parameter['description'],
                       'php_type'       => $parameter['php_type'],
                       'dtd_identifier' => $parameter['dtd_identifier']];
    }

    $this->enhancePhpDocBlockParameters($parameters);

    if (!empty($parameters))
    {
      // Compute the max lengths of parameter names and the PHP types of the parameters.
      $maxNameLength = 0;
      $maxTypeLength = 0;
      foreach ($parameters as $parameter)
      {
        $maxNameLength = max($maxNameLength, mb_strlen($parameter['php_name']));
        $maxTypeLength = max($maxTypeLength, mb_strlen($parameter['php_type']));
      }

      $context->codeStore->append(' *', false);

      // Generate phpDoc for the parameters of the wrapper method.

      foreach ($parameters as $parameter)
      {
        $format = sprintf(' * %%-%ds %%-%ds %%-%ds %%s', mb_strlen('@param'), $maxTypeLength, $maxNameLength);

        $lines = explode(PHP_EOL, $parameter['description'] ?? '');
        if (!empty($lines))
        {
          $line = array_shift($lines);
          $context->codeStore->append(sprintf($format,
                                              '@param',
                                              $parameter['php_type'],
                                              $parameter['php_name'],
                                              $line),
                                      false);
          foreach ($lines as $line)
          {
            $context->codeStore->append(sprintf($format, ' ', ' ', ' ', $line), false);
          }
        }
        else
        {
          $context->codeStore->append(sprintf($format,
                                              '@param',
                                              $parameter['php_type'],
                                              $parameter['php_name'],
                                              ''),
                                      false);
        }

        if ($parameter['dtd_identifier']!==null)
        {
          $context->codeStore->append(sprintf($format, ' ', ' ', ' ', $parameter['dtd_identifier']), false);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP doc block for the return type of the stored routine wrapper.
   *
   * @param WrapperContext $context The wrapper context.
   */
  private function generatePhpDocBlockReturn(WrapperContext $context): void
  {
    $return = $this->getDocBlockReturnType($context);
    if ($return!=='')
    {
      $context->codeStore->append(' *', false);
      $context->codeStore->append(' * @return '.$return, false);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP doc block with throw tags.
   *
   * @param WrapperContext $context The wrapper context.
   */
  private function generatePhpDocBlockThrow(WrapperContext $context): void
  {
    if (!empty($this->throws))
    {
      $context->codeStore->append(' *', false);

      $this->throws = array_unique($this->throws, SORT_REGULAR);
      foreach ($this->throws as $class)
      {
        $context->codeStore->append(sprintf(' * @throws %s', $class), false);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
