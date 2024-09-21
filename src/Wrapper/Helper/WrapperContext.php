<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Wrapper\Helper;

use SetBased\Helper\CodeStore\PhpCodeStore;
use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;
use SetBased\Stratum\Middle\NameMangler\NameMangler;

/**
 * The build context for generating wrapper methods for invoking stored routines.
 */
class WrapperContext
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The PHP code store for, well, storing the generated PHP code.
   *
   * @var PhpCodeStore
   */
  var PhpCodeStore $codeStore;

  /**
   * Helper object for deriving information based on a DBMS native data type.
   *
   * @var CommonDataTypeHelper
   */
  readonly CommonDataTypeHelper $dataType;

  /**
   * The object for mangling stored routine names to method names and stored routine parameter names to parameters
   * names in the datalayer.
   *
   * @var NameMangler
   */
  readonly NameMangler $mangler;

  /**
   * The metadata of the stored routine.
   *
   * @var array
   */
  readonly array $phpStratumMetadata;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param CommonDataTypeHelper $dataType
   * @param PhpCodeStore         $codeStore          The PHP code store for, well, storing the generated PHP code.
   * @param NameMangler          $mangler            The object for mangling stored routine names to method names and
   *                                                 stored routine parameter names to parameters names in the
   *                                                 datalayer.
   * @param array                $phpStratumMetadata The metadata of the stored routine.
   */
  public function __construct(CommonDataTypeHelper $dataType,
                              PhpCodeStore         $codeStore,
                              NameMangler          $mangler,
                              array                $phpStratumMetadata)
  {
    $this->dataType           = $dataType;
    $this->codeStore          = $codeStore;
    $this->mangler            = $mangler;
    $this->phpStratumMetadata = $phpStratumMetadata;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
