<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\DocBlock;

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use SetBased\Stratum\Common\Antlr\DocBlockLexer;
use SetBased\Stratum\Common\Antlr\DocBlockParser;
use SetBased\Stratum\Common\Helper\DocBlockHelper;

/**
 * Reflection for DocBlocks.
 */
class DocBlockReflection
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The visitor for parsing a DocBlock.
   *
   * @var DocBlockVisitor
   */
  private $visitor;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $docBlock
   */
  public function __construct(string $docBlock)
  {
    $this->visitor = new DocBlockVisitor();
    $this->reflect($docBlock);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Set the expected number of parameter of a tag
   *
   * @param string $tagName            The name of the taf.
   * @param int    $numberOfParameters The expected number of parameters.
   */
  public static function setTagParameters(string $tagName, int $numberOfParameters): void
  {
    DocBlockHelper::setTagParameters($tagName, $numberOfParameters);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the long description as an array of lines.
   *
   * @return array
   */
  public function getLongDescription(): array
  {
    return $this->visitor->getLongDescription();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the short description as an array of lines.
   *
   * @return array
   */
  public function getShortDescription(): array
  {
    return $this->visitor->getShortDescription();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the tags as an array.
   */
  public function getTags(): array
  {
    return $this->visitor->getTags();
  }
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reflect a DocBlock.
   *
   * @param string $docBlock The DocBlock.
   */
  private function reflect(string $docBlock): void
  {
    $docBlock = trim($docBlock);
    $docBlock = preg_replace('|^/\*|', '', $docBlock);
    $docBlock = preg_replace('|/$|', '', $docBlock);
    $docBlock .= PHP_EOL;

    $input  = InputStream::fromString($docBlock);
    $lexer  = new DocBlockLexer($input);
    $tokens = new CommonTokenStream($lexer);

    $parser = new DocBlockParser($tokens);
    $parser->addErrorListener(new DiagnosticErrorListener());
    $parser->setBuildParseTree(true);
    $tree = $parser->docblock();

    $this->visitor->visit($tree);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
