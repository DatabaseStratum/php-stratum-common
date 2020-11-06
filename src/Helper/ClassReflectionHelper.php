<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Helper;

use Composer\Autoload\ClassLoader;
use SetBased\Exception\RuntimeException;

/**
 * Utility class for finding the source of a class.
 */
class ClassReflectionHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the path to the source of a class.
   *
   * This method differs from PHP's \ReflectionClass::getFileName() that is does not actually load the class.
   *
   * @param string $className The fully qualified name of the class.
   *
   * @return string
   */
  public static function getFileName(string $className): string
  {
    // Get the class loader.
    $loaders = spl_autoload_functions();
    $loader  = null;
    foreach ($loaders as $tmp)
    {
      if (is_array($tmp) && is_a($tmp[0], ClassLoader::class))
      {
        $loader = $tmp[0];
      }
    }
    if ($loader===null)
    {
      throw new RuntimeException("Cannot find Composer's class loader.");
    }

    // Find the source file of the constant class.
    $fileName = $loader->findFile($className);
    if ($fileName===false)
    {
      throw new RuntimeException("ClassLoader can not find class '%s'.", $className);
    }

    return $fileName;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
