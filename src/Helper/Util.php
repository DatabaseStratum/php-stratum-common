<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Helper;

use SetBased\Stratum\Backend\StratumStyle;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * An utility class with miscellaneous functions that don't belong somewhere else.
 */
class Util
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes a file in two phase to the filesystem.
   *
   * If the file already exists and its content is equal to the data that must be written no action is taken. Otherwise,
   * first writes the data to a temporary file (in the same directory) and than renames the temporary file. This has the
   * following advantages:
   * * In case of some write error (e.g. disk full) the original file is kept in tact and no file with partially data
   * is written.
   * * Renaming a file is atomic. So, running processes will never read a partially written data.
   *
   * @param string       $filename The name of the file were the data must be stored.
   * @param string       $data     The data that must be written.
   * @param StratumStyle $io       The output object.
   */
  public static function writeTwoPhases(string $filename, string $data, StratumStyle $io): void
  {
    $write = true;
    if (file_exists($filename))
    {
      $oldData = file_get_contents($filename);
      if ($data==$oldData) $write = false;
    }

    if ($write)
    {
      $tmpFilename = $filename.'.tmp';
      file_put_contents($tmpFilename, $data);
      rename($tmpFilename, $filename);

      $io->text(sprintf('Wrote <fso>%s</fso>', OutputFormatter::escape($filename)));
    }
    else
    {
      $io->text(sprintf('File <fso>%s</fso> is up to date', OutputFormatter::escape($filename)));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
