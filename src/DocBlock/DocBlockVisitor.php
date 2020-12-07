<?php
declare(strict_types=1);

namespace SetBased\Stratum\Common\DocBlock;

use Antlr\Antlr4\Runtime\Tree\ParseTree;
use Antlr\Antlr4\Runtime\Tree\TerminalNode;
use SetBased\Exception\FallenException;
use SetBased\Helper\Cast;
use SetBased\Stratum\Common\Antlr\Context;
use SetBased\Stratum\Common\Antlr\DocBlockParserBaseVisitor;
use SetBased\Stratum\Common\Helper\DocBlockHelper;

/**
 * Visitor for DocBlocks.
 */
class DocBlockVisitor extends DocBlockParserBaseVisitor
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The long description found in the DocBlock.
   *
   * @var array
   */
  private array $longDescription = [];

  /**
   * The short description found in the DocBlock.
   *
   * @var array
   */
  private array $shortDescription = [];

  /**
   * The tags found in the DocBlock.
   *
   * @var array
   */
  private array $tags = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the long description as an array of lines.
   *
   * @return array
   */
  public function getLongDescription(): array
  {
    return $this->longDescription;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the short description as an array of lines.
   *
   * @return array
   */
  public function getShortDescription(): array
  {
    return $this->shortDescription;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the tags as an array.
   */
  public function getTags(): array
  {
    return $this->tags;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visit(ParseTree $tree): void
  {
    $this->longDescription  = [];
    $this->shortDescription = [];
    $this->tags             = [];

    $tree->accept($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Returns a single empty line.
   *
   * @return array
   */
  public function visitEmpty_lines(Context\Empty_linesContext $context): array
  {
    return [''];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Returns an empty string.
   *
   * @return string
   */
  public function visitEol(Context\EolContext $context): string
  {
    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}.
   *
   * @return string
   */
  public function visitLeading_whitespace(Context\Leading_whitespaceContext $context): string
  {
    return str_replace('*', ' ', $context->getChild(0)->accept($this));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Returns lines.
   *
   * @return array
   */
  public function visitLines(Context\LinesContext $context): array
  {
    $lines = [];
    $n     = $context->getChildCount();
    for ($i = 0; $i<$n; $i++)
    {
      $lines[] = $context->getChild($i)->accept($this);
    }

    return $lines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Parses the long description.
   */
  public function visitLong_description(Context\Long_descriptionContext $context): void
  {
    $tmp = [];
    $n   = $context->getChildCount();
    for ($i = 0; $i<$n; $i++)
    {
      $tmp = array_merge($tmp, $context->getChild($i)->accept($this));
    }

    $this->longDescription = DocBlockHelper::leftTrimBlock($tmp);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Parses the short description.
   */
  public function visitShort_description(Context\Short_descriptionContext $context): void
  {
    $tmp                    = $context->getChild(0)->accept($this);
    $this->shortDescription = DocBlockHelper::leftTrimBlock($tmp);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visitTag(Context\TagContext $context): array
  {
    $lines   = [];
    $n       = $context->getChildCount();
    $lines[] = $context->getChild(0)->accept($this);
    for ($i = 1; $i<$n; $i++)
    {
      $child = $context->getChild($i);
      $tmp   = $child->accept($this);
      switch (true)
      {
        case $child instanceof Context\Tag_lineContext:
        case $child instanceof Context\Empty_linesContext:
          $lines = array_merge($lines, $tmp);

          break;

        case $child instanceof Context\Text_lineContext:
          $lines[] = $tmp;
          break;

        default:
          throw new FallenException('class', get_class($child));
      }
    }

    return DocBlockHelper::extractTag($lines);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visitTag_line(Context\Tag_lineContext $context): string
  {
    $line = '';
    $n    = $context->getChildCount();
    for ($i = 0; $i<$n; $i++)
    {
      $line .= $context->getChild($i)->accept($this);
    }

    return rtrim($line);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visitTag_part(Context\Tag_partContext $context): string
  {
    return $context->getChild(0)->accept($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * Parses the tags.
   */
  public function visitTags(Context\TagsContext $context): void
  {
    $n = $context->getChildCount();
    for ($i = 0; $i<$n; $i++)
    {
      $child = $context->getChild($i);
      switch (true)
      {
        case $child instanceof Context\TagContext:
          $this->tags[] = $child->accept($this);
          break;

        case $child instanceof Context\Empty_linesContext:
          // Ignore empty lines.
          break;

        default:
          throw new FallenException('class', get_class($child));
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   *
   * @return string
   */
  public function visitTerminal(TerminalNode $node): string
  {
    return Cast::toManString($node->getText());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visitText_line(Context\Text_lineContext $context): string
  {
    $line = '';
    $n    = $context->getChildCount();
    for ($i = 0; $i<$n; $i++)
    {
      $line .= $context->getChild($i)->accept($this);
    }

    return rtrim($line);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function visitText_part(Context\Text_partContext $context): string
  {
    return $context->getChild(0)->accept($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
