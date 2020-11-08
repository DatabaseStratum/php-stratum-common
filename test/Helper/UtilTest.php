<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Test\Helper;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Common\Helper\Util;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Test cases for class Util.
 */
class UtilTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test case for method Util::writeTwoPhases().
   */
  public function testWriteTwoPhases(): void
  {
    $filename = 'test/Helper/hello.txt';
    $message1 = 'Hello, World!';
    $message2 = 'Bye, bye.';

    if (file_exists($filename))
    {
      unlink($filename);
    }

    $input  = new StringInput('');
    $output = new StreamOutput(fopen('php://memory', 'w', false));
    $style  = new StratumStyle($input, $output);

    Util::writeTwoPhases($filename, $message1, $style);
    Util::writeTwoPhases($filename, $message1, $style);
    Util::writeTwoPhases($filename, $message2, $style);

    rewind($output->getStream());
    $display = stream_get_contents($output->getStream());
    $lines   = explode(PHP_EOL, $display);
    self::assertSame(['',
                      ' Wrote test/Helper/hello.txt',
                      ' File test/Helper/hello.txt is up to date',
                      ' Wrote test/Helper/hello.txt',
                      ''], $lines);

    if (file_exists($filename))
    {
      unlink($filename);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
