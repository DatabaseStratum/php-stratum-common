<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Helper;

/**
 * Helper class for deriving information based on a DBMS native data type.
 */
interface CommonDataTypeHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns all column types supported by the RDBMS.
   *
   * @return array
   */
  public function allColumnTypes(): array;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the corresponding PHP data type of DBMS native data type.
   *
   * @param array $dataTypeInfo The DBMS native data type metadata.
   *
   * @return string
   */
  public function columnTypeToPhpType(array $dataTypeInfo): string;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns PHP code escaping the value of a PHP expression that can be safely used when concatenating a SQL statement.
   *
   * @param array  $dataTypeInfo Metadata of the column on which the field is based.
   * @param string $expression   The PHP expression.
   */
  public function escapePhpExpression(array $dataTypeInfo, string $expression): string;

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
