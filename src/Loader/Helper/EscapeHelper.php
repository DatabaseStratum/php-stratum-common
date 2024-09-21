<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

/**
 * Interface for escaping strings such that they are safe to use in SQL queries.
 */
interface EscapeHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Escapes a string such that is safe to use in SQL queries.
   *
   * @param string $string The string.
   *
   * @return string
   */
  public function escapeString(string $string): string;

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
