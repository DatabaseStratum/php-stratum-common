<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Test\Helper;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Common\Helper\ClassReflectionHelper;
use SetBased\Stratum\Common\Test\Helper\Foo\Bar;

/**
 * Test cases for class ClassReflectionHelper.
 */
class ClassReflectionHelperTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test method ClassReflectionHelper::getFileName() return the filename without loading the class.
   */
  public function testGetFilename(): void
  {
    $filename = ClassReflectionHelper::getFileName(Bar::class);
    self::assertSame(realpath('test/Helper/Foo/Bar.php'), realpath($filename));

    // Test class is not loaded.
    self::assertFalse(defined('SetBased\Stratum\Common\Test\Helper\Foo\AUTO_LOADER_WAS_HERE'));

    // Test source does define AUTO_LOADER_WAS_HERE.
    new Bar();
    self::assertTrue(defined('SetBased\Stratum\Common\Test\Helper\Foo\AUTO_LOADER_WAS_HERE'));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
