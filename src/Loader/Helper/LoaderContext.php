<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Loader\Helper;

use SetBased\Stratum\Common\Helper\CommonDataTypeHelper;

/**
 * The build context for loading stored routines.
 */
class LoaderContext
{
  //--------------------------------------------------------------------------------------------------------------------
  readonly CommonDataTypeHelper $dataType;

  readonly DocBlockHelper $docBlock;

  var array $newPhpStratumMetadata;

  readonly array $oldPhpStratumMetadata;

  readonly array $oldRdbmsMetadata;

  readonly PlaceholderHelper $placeHolders;

  readonly StoredRoutineHelper $storedRoutine;

  readonly TypeHintHelper $typeHints;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param CommonDataTypeHelper $dataType
   * @param StoredRoutineHelper  $storedRoutine
   * @param TypeHintHelper       $typeHints
   * @param DocBlockHelper       $docBlock
   * @param PlaceholderHelper    $placeHolders
   * @param array                $oldRdbmsMetadata
   * @param array                $oldPhpStratumMetadata
   * @param array                $newPhpStratumMetadata
   */
  public function __construct(CommonDataTypeHelper $dataType,
                              StoredRoutineHelper  $storedRoutine,
                              TypeHintHelper       $typeHints,
                              DocBlockHelper       $docBlock,
                              PlaceholderHelper    $placeHolders,
                              array                $oldRdbmsMetadata,
                              array                $oldPhpStratumMetadata,
                              array                $newPhpStratumMetadata)
  {
    $this->dataType              = $dataType;
    $this->storedRoutine         = $storedRoutine;
    $this->typeHints             = $typeHints;
    $this->docBlock              = $docBlock;
    $this->placeHolders          = $placeHolders;
    $this->oldRdbmsMetadata      = $oldRdbmsMetadata;
    $this->oldPhpStratumMetadata = $oldPhpStratumMetadata;
    $this->newPhpStratumMetadata = $newPhpStratumMetadata;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
