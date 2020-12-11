<?php

/*
 * Generated from src/Antlr/DocBlockParser.g4 by ANTLR 4.9
 */

namespace SetBased\Stratum\Common\Antlr;

use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;

/**
 * This interface defines a complete generic visitor for a parse tree produced by {@see DocBlockParser}.
 */
interface DocBlockParserVisitor extends ParseTreeVisitor
{
	/**
	 * Visit a parse tree produced by {@see DocBlockParser::docblock()}.
	 *
	 * @param Context\DocblockContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDocblock(Context\DocblockContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::description()}.
	 *
	 * @param Context\DescriptionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDescription(Context\DescriptionContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::short_description()}.
	 *
	 * @param Context\Short_descriptionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitShort_description(Context\Short_descriptionContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::long_description()}.
	 *
	 * @param Context\Long_descriptionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLong_description(Context\Long_descriptionContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::lines()}.
	 *
	 * @param Context\LinesContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLines(Context\LinesContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::text_line()}.
	 *
	 * @param Context\Text_lineContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitText_line(Context\Text_lineContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::text_part()}.
	 *
	 * @param Context\Text_partContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitText_part(Context\Text_partContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::tags()}.
	 *
	 * @param Context\TagsContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTags(Context\TagsContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::tag()}.
	 *
	 * @param Context\TagContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTag(Context\TagContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::tag_line()}.
	 *
	 * @param Context\Tag_lineContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTag_line(Context\Tag_lineContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::tag_part()}.
	 *
	 * @param Context\Tag_partContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTag_part(Context\Tag_partContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::empty_lines()}.
	 *
	 * @param Context\Empty_linesContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitEmpty_lines(Context\Empty_linesContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::leading_whitespace()}.
	 *
	 * @param Context\Leading_whitespaceContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLeading_whitespace(Context\Leading_whitespaceContext $context);

	/**
	 * Visit a parse tree produced by {@see DocBlockParser::eol()}.
	 *
	 * @param Context\EolContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitEol(Context\EolContext $context);
}