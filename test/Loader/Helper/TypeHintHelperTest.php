<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Test\Loader\Helper;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Common\Loader\Helper\TypeHintHelper;

/**
 * Test cases for class TypeHintHelper.
 */
class TypeHintHelperTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for a type hint with not null;
   */
  public function testTypeHintDefault(): void
  {
    $code = <<<CODE
  declare l_var1 int default 1; -- type: TABLE1.col11
  declare l_var2 int default 2 -- type: TABLE2.col21
  ;
  
  create temporary table TMP_TABLE1
  (
     col11 int not null, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 int not null -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $expected = <<<CODE
  declare l_var1 int unsigned default 1; --  type: TABLE1.col11
  declare l_var2 enum('A', 'B') default 2 -- type: TABLE2.col21
  ;
  
  create temporary table TMP_TABLE1
  (
     col11 int unsigned not null, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 enum('A', 'B') not null -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $code2 = $this->updateTypeHints($code);
    self::assertSame($expected, $code2);

    $code3 = $this->updateTypeHints($code2);
    self::assertSame($expected, $code3);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for a type hint with not null;
   */
  public function testTypeHintNotAndDefaultDefault(): void
  {
    $code = <<<CODE
  --
  create temporary table TMP_TABLE1
  (
     col11 int not null default 1, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 int not null default 2 -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $expected = <<<CODE
  --
  create temporary table TMP_TABLE1
  (
     col11 int unsigned not null default 1, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 enum('A', 'B') not null default 2 -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $code2 = $this->updateTypeHints($code);
    self::assertSame($expected, $code2);

    $code3 = $this->updateTypeHints($code2);
    self::assertSame($expected, $code3);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for a type hint with not null;
   */
  public function testTypeHintNotNull(): void
  {
    $code = <<<CODE
  --
  create temporary table TMP_TABLE1
  (
     col11 int not null, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 int not null -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $expected = <<<CODE
  --
  create temporary table TMP_TABLE1
  (
     col11 int unsigned not null, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 enum('A', 'B') not null -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $code2 = $this->updateTypeHints($code);
    self::assertSame($expected, $code2);

    $code3 = $this->updateTypeHints($code2);
    self::assertSame($expected, $code3);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for a type hint without extra code.
   */
  public function testTypeHintPlain(): void
  {
    $code = <<<CODE
  declare l_var1 int; -- type: TABLE1.col11
  declare l_var2 int --  type: TABLE2.col21
  ;

  create temporary table TMP_TABLE1
  (
     col11 int, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 int -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $expected = <<<CODE
  declare l_var1 int unsigned; --  type: TABLE1.col11
  declare l_var2 enum('A', 'B') -- type: TABLE2.col21
  ;

  create temporary table TMP_TABLE1
  (
     col11 int unsigned, -- type: TABLE1.col11
     primary key(col11)
  );

  create temporary table TMP_TABLE2
  (
     col21 enum('A', 'B') -- type: TABLE2.col21
   , primary key(col21)
  );
CODE;

    $code2 = $this->updateTypeHints($code);
    self::assertSame($expected, $code2);

    $code3 = $this->updateTypeHints($code2);
    self::assertSame($expected, $code3);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs the TypeHintHelper on a piece of code and returns the modified code.
   *
   * @param string $code The piece of code.
   */
  private function updateTypeHints(string $code): string
  {
    $typeHintHelper = new TypeHintHelper();
    $typeHintHelper->addTypeHint('TABLE1.col11', 'int unsigned');
    $typeHintHelper->addTypeHint('TABLE2.col21', "enum('A', 'B')");
    $dataTypeHelper = new TestDataTypeHelper();
    $lines          = $typeHintHelper->updateTypes(explode(PHP_EOL, $code), $dataTypeHelper);
    $lines          = $typeHintHelper->alignTypeHints($lines);

    return implode(PHP_EOL, $lines);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
