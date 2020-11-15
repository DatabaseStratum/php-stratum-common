<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\Test\DocBlock;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Common\DocBlock\DocBlockReflection;

/**
 * Test case for bulk inserts.
 */
class DocBlockReflectionTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns empty DocBlocks.
   *
   * @return array
   */
  public static function emptyDocBlocks(): array
  {
    return [[''], [' '], ['/***/'], [' /***/'], ['/***/ '], ["/**\n*/"], ["\t/**\n*/\n"]];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short and long description, and tags.
   */
  public function testDocBlock1(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     *
     * The is a long description of one line.
     * This is line 2.
     *
     * 
    *This is line 1 of second paragraph with wrong indentation.
     *
     * @param p1 Description 1
     * @param p2 Description 2
     *           On multiple lines.
     * @param p3 Description 3
     *
     * @todo Todo ...
     */
    EOT;

    DocBlockReflection::setTagParameters('param', ['name']);

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame(['The is a long description of one line.',
                        'This is line 2.',
                        '',
                        'This is line 1 of second paragraph with wrong indentation.'],
                       $reflection->getLongDescription());
    self::assertSame([['tag'         => 'param',
                         'arguments'   => ['name' => 'p1'],
                         'description' => ['Description 1']],
                        ['tag'         => 'param',
                         'arguments'   => ['name' => 'p2'],
                         'description' => ['Description 2',
                                           'On multiple lines.']],
                        ['tag'         => 'param',
                         'arguments'   => ['name' => 'p3'],
                         'description' => ['Description 3']],
                        ['tag'         => 'todo',
                         'arguments'   => [],
                         'description' => ['Todo ...']]], $reflection->getTags());
    self::assertSame([['tag'         => 'todo',
                         'arguments'   => [],
                         'description' => ['Todo ...']]], $reflection->getTags('todo'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short and long description, and tags.
   */
  public function testDocBlock2(): void
  {
    $docBlock = <<< EOT
    /**
     * Test for designation type row0 with BLOB.
     *
     * @param p_count The number of rows selected.
     *                * 0 For a valid test.
     *                * 1 For a valid test.
     *                * 2 For a invalid test.
     * @param p_blob  The BLOB.
     */
    EOT;

    DocBlockReflection::setTagParameters('param', ['name']);

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['Test for designation type row0 with BLOB.'], $reflection->getShortDescription());
    self::assertSame([], $reflection->getLongDescription());
    self::assertSame([['tag'         => 'param',
                         'arguments'   => ['name' => 'p_count'],
                         'description' => ['The number of rows selected.',
                                           '* 0 For a valid test.',
                                           '* 1 For a valid test.',
                                           '* 2 For a invalid test.']],
                        ['tag'         => 'param',
                         'arguments'   => ['name' => 'p_blob'],
                         'description' => ['The BLOB.']]], $reflection->getTags());
    self::assertSame([], $reflection->getTags('todo'));

  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short and long description, and tags.
   */
  public function testParams(): void
  {
    $docBlock = <<< EOT
    /**
     * @param int p_count This an integer or int for short.
     */
    EOT;

    DocBlockReflection::setTagParameters('param', ['type', 'name']);

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame([['tag'         => 'param',
                       'arguments'   => ['type' => 'int', 'name' => 'p_count'],
                       'description' => ['This an integer or int for short.']]], $reflection->getTags());
    self::assertSame([], $reflection->getTags('todo'));

  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test empty DocBlock.
   *
   * @param string $docBlock
   *
   * @dataProvider emptyDocBlocks
   */
  public function testEmptyDocBlock1(string $docBlock): void
  {
    $reflection = new DocBlockReflection($docBlock);

    self::assertSame([], $reflection->getShortDescription(), $docBlock);
    self::assertSame([], $reflection->getLongDescription(), $docBlock);
    self::assertSame([], $reflection->getTags(), $docBlock);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short and long description.
   */
  public function testLongDescriptionOnly1(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     *
     * The is a long description of one line.
     */
    EOT;

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame(['The is a long description of one line.'], $reflection->getLongDescription());
    self::assertSame([], $reflection->getTags());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short and long description.
   */
  public function testLongDescriptionOnly2(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     *
     * The is a long description of one line.
     * This is line 2.
     *
     * 
    *This is line 1 of second paragraph with wrong indentation.
     */
    EOT;

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame(['The is a long description of one line.',
                        'This is line 2.',
                        '',
                        'This is line 1 of second paragraph with wrong indentation.'],
                       $reflection->getLongDescription());
    self::assertSame([], $reflection->getTags());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short description and empty line.
   */
  public function testNastyLongDescription(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     *
    N@sty tag confusion.
     *
     */
    EOT;

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame(['N@sty tag confusion.'], $reflection->getLongDescription());
    self::assertSame([], $reflection->getTags());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test against a random string.
   */
  public function testRandomString(): void
  {
    $length   = 10000;
    $pool     = "0123456789@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\n \t*\r";
    $docBlock = substr(str_shuffle(str_repeat($pool, (int)ceil($length / strlen($pool)))), 1, $length);

    try
    {
      new DocBlockReflection($docBlock);

      $test = true;
    }
    catch (\Throwable $exception)
    {
      $test = false;
    }

    self::assertTrue($test, $docBlock);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short description and empty line.
   */
  public function testShortDescriptionEmptyLine(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     *
     */
    EOT;

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame([], $reflection->getLongDescription());
    self::assertSame([], $reflection->getTags());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test DocBlock with short description only.
   */
  public function testShortDescriptionOnly(): void
  {
    $docBlock = <<< EOT
    /**
     * This is a shot description.
     */
    EOT;

    $reflection = new DocBlockReflection($docBlock);

    self::assertSame(['This is a shot description.'], $reflection->getShortDescription());
    self::assertSame([], $reflection->getLongDescription());
    self::assertSame([], $reflection->getTags());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
