parser grammar DocBlockParser;

options { tokenVocab=DocBlockLexer; }

// DocBlock.
docblock: empty_lines? description? empty_lines? tags? empty_lines? EOF;


// Short and long block.
description: short_description (empty_lines long_description)?;

short_description: lines;

long_description: lines (empty_lines lines)*;


// Normal text.
lines: text_line+;

text_line: leading_whitespace? text_part eol;

text_part: TEXT_PART;

// Tags.
tags: (tag empty_lines?)+;

tag: tag_line ((text_line|empty_lines)* text_line)?;

tag_line: leading_whitespace? tag_part eol;

tag_part: TAG_PART;


// Whitespace,  empty lines, and EOL.
empty_lines: (leading_whitespace? eol)+;

leading_whitespace: LEADING_WHITESPACE;

eol: EOL;
