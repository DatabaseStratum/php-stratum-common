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
  private DocBlockVisitor $visitor;

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
   * Sets the names of the parameters of a tag.
   *
   * @param string   $tagName The name of the tag.
   * @param string[] $names   The names of the parameters.
   */
  public static function setTagParameters(string $tagName, array $names): void
  {
    DocBlockHelper::setTagParameters($tagName, $names);
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
   *
   * @param string|null $filter If not null only tags with name equal to this filter are returns.
   *
   * @return array
   */
  public function getTags(?string $filter=null): array
  {
    if ($filter===null)
    {
      return $this->visitor->getTags();
    }

    $ret = [];
    foreach ($this->visitor->getTags() as $tag)
    {
      if ($tag['tag']===$filter)
      {
        $ret[] = $tag;
      }
    }

    return $ret;
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
