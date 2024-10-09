<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Test\Loader\Helper;

use SetBased\Exception\LogicException;
use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;

/**
 * Implementation of CommonDataTypeHelper for testing purposes.
 */
class TestDataTypeHelper implements CommonDataTypeHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function allColumnTypes(): array
  {
    return ['enum',
            'int',
            'varchar'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function columnTypeToPhpType(array $dataTypeInfo): string
  {
    throw new LogicException('Not implemented');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function escapePhpExpression(array $dataTypeInfo, string $expression): string
  {
    throw new LogicException('Not implemented');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
