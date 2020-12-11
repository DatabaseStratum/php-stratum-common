<?php

/*
 * Generated from src/Antlr/DocBlockParser.g4 by ANTLR 4.9
 */

namespace SetBased\Stratum\Common\Antlr {
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
	use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\TokenStream;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\VocabularyImpl;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\Parser;

	final class DocBlockParser extends Parser
	{
		public const EOL = 1, LEADING_WHITESPACE = 2, TAG_PART = 3, TEXT_PART = 4, 
               AT1 = 5;

		public const RULE_docblock = 0, RULE_description = 1, RULE_short_description = 2, 
               RULE_long_description = 3, RULE_lines = 4, RULE_text_line = 5, 
               RULE_text_part = 6, RULE_tags = 7, RULE_tag = 8, RULE_tag_line = 9, 
               RULE_tag_part = 10, RULE_empty_lines = 11, RULE_leading_whitespace = 12, 
               RULE_eol = 13;

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'docblock', 'description', 'short_description', 'long_description', 'lines', 
			'text_line', 'text_part', 'tags', 'tag', 'tag_line', 'tag_part', 'empty_lines', 
			'leading_whitespace', 'eol'
		];

		/**
		 * @var array<string|null>
		 */
		private const LITERAL_NAMES = [
		];

		/**
		 * @var array<string>
		 */
		private const SYMBOLIC_NAMES = [
		    null, "EOL", "LEADING_WHITESPACE", "TAG_PART", "TEXT_PART", "AT1"
		];

		/**
		 * @var string
		 */
		private const SERIALIZED_ATN =
			"\u{3}\u{608B}\u{A72A}\u{8133}\u{B9ED}\u{417C}\u{3BE7}\u{7786}\u{5964}" .
		    "\u{3}\u{7}\u{75}\u{4}\u{2}\u{9}\u{2}\u{4}\u{3}\u{9}\u{3}\u{4}\u{4}" .
		    "\u{9}\u{4}\u{4}\u{5}\u{9}\u{5}\u{4}\u{6}\u{9}\u{6}\u{4}\u{7}\u{9}" .
		    "\u{7}\u{4}\u{8}\u{9}\u{8}\u{4}\u{9}\u{9}\u{9}\u{4}\u{A}\u{9}\u{A}" .
		    "\u{4}\u{B}\u{9}\u{B}\u{4}\u{C}\u{9}\u{C}\u{4}\u{D}\u{9}\u{D}\u{4}" .
		    "\u{E}\u{9}\u{E}\u{4}\u{F}\u{9}\u{F}\u{3}\u{2}\u{5}\u{2}\u{20}\u{A}" .
		    "\u{2}\u{3}\u{2}\u{5}\u{2}\u{23}\u{A}\u{2}\u{3}\u{2}\u{5}\u{2}\u{26}" .
		    "\u{A}\u{2}\u{3}\u{2}\u{5}\u{2}\u{29}\u{A}\u{2}\u{3}\u{2}\u{5}\u{2}" .
		    "\u{2C}\u{A}\u{2}\u{3}\u{2}\u{3}\u{2}\u{3}\u{3}\u{3}\u{3}\u{3}\u{3}" .
		    "\u{3}\u{3}\u{5}\u{3}\u{34}\u{A}\u{3}\u{3}\u{4}\u{3}\u{4}\u{3}\u{5}" .
		    "\u{3}\u{5}\u{3}\u{5}\u{3}\u{5}\u{7}\u{5}\u{3C}\u{A}\u{5}\u{C}\u{5}" .
		    "\u{E}\u{5}\u{3F}\u{B}\u{5}\u{3}\u{6}\u{6}\u{6}\u{42}\u{A}\u{6}\u{D}" .
		    "\u{6}\u{E}\u{6}\u{43}\u{3}\u{7}\u{5}\u{7}\u{47}\u{A}\u{7}\u{3}\u{7}" .
		    "\u{3}\u{7}\u{3}\u{7}\u{3}\u{8}\u{3}\u{8}\u{3}\u{9}\u{3}\u{9}\u{5}" .
		    "\u{9}\u{50}\u{A}\u{9}\u{6}\u{9}\u{52}\u{A}\u{9}\u{D}\u{9}\u{E}\u{9}" .
		    "\u{53}\u{3}\u{A}\u{3}\u{A}\u{3}\u{A}\u{7}\u{A}\u{59}\u{A}\u{A}\u{C}" .
		    "\u{A}\u{E}\u{A}\u{5C}\u{B}\u{A}\u{3}\u{A}\u{5}\u{A}\u{5F}\u{A}\u{A}" .
		    "\u{3}\u{B}\u{5}\u{B}\u{62}\u{A}\u{B}\u{3}\u{B}\u{3}\u{B}\u{3}\u{B}" .
		    "\u{3}\u{C}\u{3}\u{C}\u{3}\u{D}\u{5}\u{D}\u{6A}\u{A}\u{D}\u{3}\u{D}" .
		    "\u{6}\u{D}\u{6D}\u{A}\u{D}\u{D}\u{D}\u{E}\u{D}\u{6E}\u{3}\u{E}\u{3}" .
		    "\u{E}\u{3}\u{F}\u{3}\u{F}\u{3}\u{F}\u{2}\u{2}\u{10}\u{2}\u{4}\u{6}" .
		    "\u{8}\u{A}\u{C}\u{E}\u{10}\u{12}\u{14}\u{16}\u{18}\u{1A}\u{1C}\u{2}" .
		    "\u{2}\u{2}\u{77}\u{2}\u{1F}\u{3}\u{2}\u{2}\u{2}\u{4}\u{2F}\u{3}\u{2}" .
		    "\u{2}\u{2}\u{6}\u{35}\u{3}\u{2}\u{2}\u{2}\u{8}\u{37}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{A}\u{41}\u{3}\u{2}\u{2}\u{2}\u{C}\u{46}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{E}\u{4B}\u{3}\u{2}\u{2}\u{2}\u{10}\u{51}\u{3}\u{2}\u{2}\u{2}\u{12}" .
		    "\u{55}\u{3}\u{2}\u{2}\u{2}\u{14}\u{61}\u{3}\u{2}\u{2}\u{2}\u{16}\u{66}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{18}\u{6C}\u{3}\u{2}\u{2}\u{2}\u{1A}\u{70}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{1C}\u{72}\u{3}\u{2}\u{2}\u{2}\u{1E}\u{20}\u{5}\u{18}" .
		    "\u{D}\u{2}\u{1F}\u{1E}\u{3}\u{2}\u{2}\u{2}\u{1F}\u{20}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{20}\u{22}\u{3}\u{2}\u{2}\u{2}\u{21}\u{23}\u{5}\u{4}\u{3}\u{2}" .
		    "\u{22}\u{21}\u{3}\u{2}\u{2}\u{2}\u{22}\u{23}\u{3}\u{2}\u{2}\u{2}\u{23}" .
		    "\u{25}\u{3}\u{2}\u{2}\u{2}\u{24}\u{26}\u{5}\u{18}\u{D}\u{2}\u{25}" .
		    "\u{24}\u{3}\u{2}\u{2}\u{2}\u{25}\u{26}\u{3}\u{2}\u{2}\u{2}\u{26}\u{28}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{27}\u{29}\u{5}\u{10}\u{9}\u{2}\u{28}\u{27}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{28}\u{29}\u{3}\u{2}\u{2}\u{2}\u{29}\u{2B}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{2A}\u{2C}\u{5}\u{18}\u{D}\u{2}\u{2B}\u{2A}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{2B}\u{2C}\u{3}\u{2}\u{2}\u{2}\u{2C}\u{2D}\u{3}\u{2}" .
		    "\u{2}\u{2}\u{2D}\u{2E}\u{7}\u{2}\u{2}\u{3}\u{2E}\u{3}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{2F}\u{33}\u{5}\u{6}\u{4}\u{2}\u{30}\u{31}\u{5}\u{18}\u{D}" .
		    "\u{2}\u{31}\u{32}\u{5}\u{8}\u{5}\u{2}\u{32}\u{34}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{33}\u{30}\u{3}\u{2}\u{2}\u{2}\u{33}\u{34}\u{3}\u{2}\u{2}\u{2}\u{34}" .
		    "\u{5}\u{3}\u{2}\u{2}\u{2}\u{35}\u{36}\u{5}\u{A}\u{6}\u{2}\u{36}\u{7}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{37}\u{3D}\u{5}\u{A}\u{6}\u{2}\u{38}\u{39}\u{5}" .
		    "\u{18}\u{D}\u{2}\u{39}\u{3A}\u{5}\u{A}\u{6}\u{2}\u{3A}\u{3C}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{3B}\u{38}\u{3}\u{2}\u{2}\u{2}\u{3C}\u{3F}\u{3}\u{2}" .
		    "\u{2}\u{2}\u{3D}\u{3B}\u{3}\u{2}\u{2}\u{2}\u{3D}\u{3E}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{3E}\u{9}\u{3}\u{2}\u{2}\u{2}\u{3F}\u{3D}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{40}\u{42}\u{5}\u{C}\u{7}\u{2}\u{41}\u{40}\u{3}\u{2}\u{2}\u{2}\u{42}" .
		    "\u{43}\u{3}\u{2}\u{2}\u{2}\u{43}\u{41}\u{3}\u{2}\u{2}\u{2}\u{43}\u{44}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{44}\u{B}\u{3}\u{2}\u{2}\u{2}\u{45}\u{47}\u{5}" .
		    "\u{1A}\u{E}\u{2}\u{46}\u{45}\u{3}\u{2}\u{2}\u{2}\u{46}\u{47}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{47}\u{48}\u{3}\u{2}\u{2}\u{2}\u{48}\u{49}\u{5}\u{E}" .
		    "\u{8}\u{2}\u{49}\u{4A}\u{5}\u{1C}\u{F}\u{2}\u{4A}\u{D}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{4B}\u{4C}\u{7}\u{6}\u{2}\u{2}\u{4C}\u{F}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{4D}\u{4F}\u{5}\u{12}\u{A}\u{2}\u{4E}\u{50}\u{5}\u{18}\u{D}\u{2}" .
		    "\u{4F}\u{4E}\u{3}\u{2}\u{2}\u{2}\u{4F}\u{50}\u{3}\u{2}\u{2}\u{2}\u{50}" .
		    "\u{52}\u{3}\u{2}\u{2}\u{2}\u{51}\u{4D}\u{3}\u{2}\u{2}\u{2}\u{52}\u{53}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{53}\u{51}\u{3}\u{2}\u{2}\u{2}\u{53}\u{54}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{54}\u{11}\u{3}\u{2}\u{2}\u{2}\u{55}\u{5E}\u{5}\u{14}" .
		    "\u{B}\u{2}\u{56}\u{59}\u{5}\u{C}\u{7}\u{2}\u{57}\u{59}\u{5}\u{18}" .
		    "\u{D}\u{2}\u{58}\u{56}\u{3}\u{2}\u{2}\u{2}\u{58}\u{57}\u{3}\u{2}\u{2}" .
		    "\u{2}\u{59}\u{5C}\u{3}\u{2}\u{2}\u{2}\u{5A}\u{58}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{5A}\u{5B}\u{3}\u{2}\u{2}\u{2}\u{5B}\u{5D}\u{3}\u{2}\u{2}\u{2}\u{5C}" .
		    "\u{5A}\u{3}\u{2}\u{2}\u{2}\u{5D}\u{5F}\u{5}\u{C}\u{7}\u{2}\u{5E}\u{5A}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{5E}\u{5F}\u{3}\u{2}\u{2}\u{2}\u{5F}\u{13}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{60}\u{62}\u{5}\u{1A}\u{E}\u{2}\u{61}\u{60}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{61}\u{62}\u{3}\u{2}\u{2}\u{2}\u{62}\u{63}\u{3}\u{2}" .
		    "\u{2}\u{2}\u{63}\u{64}\u{5}\u{16}\u{C}\u{2}\u{64}\u{65}\u{5}\u{1C}" .
		    "\u{F}\u{2}\u{65}\u{15}\u{3}\u{2}\u{2}\u{2}\u{66}\u{67}\u{7}\u{5}\u{2}" .
		    "\u{2}\u{67}\u{17}\u{3}\u{2}\u{2}\u{2}\u{68}\u{6A}\u{5}\u{1A}\u{E}" .
		    "\u{2}\u{69}\u{68}\u{3}\u{2}\u{2}\u{2}\u{69}\u{6A}\u{3}\u{2}\u{2}\u{2}" .
		    "\u{6A}\u{6B}\u{3}\u{2}\u{2}\u{2}\u{6B}\u{6D}\u{5}\u{1C}\u{F}\u{2}" .
		    "\u{6C}\u{69}\u{3}\u{2}\u{2}\u{2}\u{6D}\u{6E}\u{3}\u{2}\u{2}\u{2}\u{6E}" .
		    "\u{6C}\u{3}\u{2}\u{2}\u{2}\u{6E}\u{6F}\u{3}\u{2}\u{2}\u{2}\u{6F}\u{19}" .
		    "\u{3}\u{2}\u{2}\u{2}\u{70}\u{71}\u{7}\u{4}\u{2}\u{2}\u{71}\u{1B}\u{3}" .
		    "\u{2}\u{2}\u{2}\u{72}\u{73}\u{7}\u{3}\u{2}\u{2}\u{73}\u{1D}\u{3}\u{2}" .
		    "\u{2}\u{2}\u{13}\u{1F}\u{22}\u{25}\u{28}\u{2B}\u{33}\u{3D}\u{43}\u{46}" .
		    "\u{4F}\u{53}\u{58}\u{5A}\u{5E}\u{61}\u{69}\u{6E}";

		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;

		public function __construct(TokenStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
		}

		private static function initialize() : void
		{
			if (self::$atn !== null) {
				return;
			}

			RuntimeMetaData::checkVersion('4.9', RuntimeMetaData::VERSION);

			$atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

			$decisionToDFA = [];
			for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
				$decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
			}

			self::$atn = $atn;
			self::$decisionToDFA = $decisionToDFA;
			self::$sharedContextCache = new PredictionContextCache();
		}

		public function getGrammarFileName() : string
		{
			return "DocBlockParser.g4";
		}

		public function getRuleNames() : array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN() : string
		{
			return self::SERIALIZED_ATN;
		}

		public function getATN() : ATN
		{
			return self::$atn;
		}

		public function getVocabulary() : Vocabulary
        {
            static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

		/**
		 * @throws RecognitionException
		 */
		public function docblock() : Context\DocblockContext
		{
		    $localContext = new Context\DocblockContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 0, self::RULE_docblock);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(29);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 0, $this->ctx)) {
		            case 1:
		        	    $this->setState(28);
		        	    $this->empty_lines();
		        	break;
		        }
		        $this->setState(32);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 1, $this->ctx)) {
		            case 1:
		        	    $this->setState(31);
		        	    $this->description();
		        	break;
		        }
		        $this->setState(35);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx)) {
		            case 1:
		        	    $this->setState(34);
		        	    $this->empty_lines();
		        	break;
		        }
		        $this->setState(38);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 3, $this->ctx)) {
		            case 1:
		        	    $this->setState(37);
		        	    $this->tags();
		        	break;
		        }
		        $this->setState(41);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::EOL || $_la === self::LEADING_WHITESPACE) {
		        	$this->setState(40);
		        	$this->empty_lines();
		        }
		        $this->setState(43);
		        $this->match(self::EOF);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function description() : Context\DescriptionContext
		{
		    $localContext = new Context\DescriptionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 2, self::RULE_description);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(45);
		        $this->short_description();
		        $this->setState(49);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 5, $this->ctx)) {
		            case 1:
		        	    $this->setState(46);
		        	    $this->empty_lines();
		        	    $this->setState(47);
		        	    $this->long_description();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function short_description() : Context\Short_descriptionContext
		{
		    $localContext = new Context\Short_descriptionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 4, self::RULE_short_description);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(51);
		        $this->lines();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function long_description() : Context\Long_descriptionContext
		{
		    $localContext = new Context\Long_descriptionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 6, self::RULE_long_description);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(53);
		        $this->lines();
		        $this->setState(59);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(54);
		        		$this->empty_lines();
		        		$this->setState(55);
		        		$this->lines(); 
		        	}

		        	$this->setState(61);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 6, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function lines() : Context\LinesContext
		{
		    $localContext = new Context\LinesContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 8, self::RULE_lines);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(63); 
		        $this->errorHandler->sync($this);

		        $alt = 1;

		        do {
		        	switch ($alt) {
		        	case 1:
		        		$this->setState(62);
		        		$this->text_line();
		        		break;
		        	default:
		        		throw new NoViableAltException($this);
		        	}

		        	$this->setState(65); 
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 7, $this->ctx);
		        } while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function text_line() : Context\Text_lineContext
		{
		    $localContext = new Context\Text_lineContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 10, self::RULE_text_line);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(68);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::LEADING_WHITESPACE) {
		        	$this->setState(67);
		        	$this->leading_whitespace();
		        }
		        $this->setState(70);
		        $this->text_part();
		        $this->setState(71);
		        $this->eol();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function text_part() : Context\Text_partContext
		{
		    $localContext = new Context\Text_partContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 12, self::RULE_text_part);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(73);
		        $this->match(self::TEXT_PART);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function tags() : Context\TagsContext
		{
		    $localContext = new Context\TagsContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 14, self::RULE_tags);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(79); 
		        $this->errorHandler->sync($this);

		        $alt = 1;

		        do {
		        	switch ($alt) {
		        	case 1:
		        		$this->setState(75);
		        		$this->tag();
		        		$this->setState(77);
		        		$this->errorHandler->sync($this);

		        		switch ($this->getInterpreter()->adaptivePredict($this->input, 9, $this->ctx)) {
		        		    case 1:
		        			    $this->setState(76);
		        			    $this->empty_lines();
		        			break;
		        		}
		        		break;
		        	default:
		        		throw new NoViableAltException($this);
		        	}

		        	$this->setState(81); 
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 10, $this->ctx);
		        } while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function tag() : Context\TagContext
		{
		    $localContext = new Context\TagContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 16, self::RULE_tag);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(83);
		        $this->tag_line();
		        $this->setState(92);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 13, $this->ctx)) {
		            case 1:
		        	    $this->setState(88);
		        	    $this->errorHandler->sync($this);

		        	    $alt = $this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx);

		        	    while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	    	if ($alt === 1) {
		        	    		$this->setState(86);
		        	    		$this->errorHandler->sync($this);

		        	    		switch ($this->getInterpreter()->adaptivePredict($this->input, 11, $this->ctx)) {
		        	    			case 1:
		        	    			    $this->setState(84);
		        	    			    $this->text_line();
		        	    			break;

		        	    			case 2:
		        	    			    $this->setState(85);
		        	    			    $this->empty_lines();
		        	    			break;
		        	    		} 
		        	    	}

		        	    	$this->setState(90);
		        	    	$this->errorHandler->sync($this);

		        	    	$alt = $this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx);
		        	    }
		        	    $this->setState(91);
		        	    $this->text_line();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function tag_line() : Context\Tag_lineContext
		{
		    $localContext = new Context\Tag_lineContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 18, self::RULE_tag_line);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(95);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::LEADING_WHITESPACE) {
		        	$this->setState(94);
		        	$this->leading_whitespace();
		        }
		        $this->setState(97);
		        $this->tag_part();
		        $this->setState(98);
		        $this->eol();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function tag_part() : Context\Tag_partContext
		{
		    $localContext = new Context\Tag_partContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 20, self::RULE_tag_part);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(100);
		        $this->match(self::TAG_PART);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function empty_lines() : Context\Empty_linesContext
		{
		    $localContext = new Context\Empty_linesContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 22, self::RULE_empty_lines);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(106); 
		        $this->errorHandler->sync($this);

		        $alt = 1;

		        do {
		        	switch ($alt) {
		        	case 1:
		        		$this->setState(103);
		        		$this->errorHandler->sync($this);
		        		$_la = $this->input->LA(1);

		        		if ($_la === self::LEADING_WHITESPACE) {
		        			$this->setState(102);
		        			$this->leading_whitespace();
		        		}
		        		$this->setState(105);
		        		$this->eol();
		        		break;
		        	default:
		        		throw new NoViableAltException($this);
		        	}

		        	$this->setState(108); 
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 16, $this->ctx);
		        } while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function leading_whitespace() : Context\Leading_whitespaceContext
		{
		    $localContext = new Context\Leading_whitespaceContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 24, self::RULE_leading_whitespace);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(110);
		        $this->match(self::LEADING_WHITESPACE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function eol() : Context\EolContext
		{
		    $localContext = new Context\EolContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 26, self::RULE_eol);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(112);
		        $this->match(self::EOL);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}
	}
}

namespace SetBased\Stratum\Common\Antlr\Context {
	use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use SetBased\Stratum\Common\Antlr\DocBlockParser;
	use SetBased\Stratum\Common\Antlr\DocBlockParserVisitor;

	class DocblockContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_docblock;
	    }

	    public function EOF() : ?TerminalNode
	    {
	        return $this->getToken(DocBlockParser::EOF, 0);
	    }

	    /**
	     * @return array<Empty_linesContext>|Empty_linesContext|null
	     */
	    public function empty_lines(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Empty_linesContext::class);
	    	}

	        return $this->getTypedRuleContext(Empty_linesContext::class, $index);
	    }

	    public function description() : ?DescriptionContext
	    {
	    	return $this->getTypedRuleContext(DescriptionContext::class, 0);
	    }

	    public function tags() : ?TagsContext
	    {
	    	return $this->getTypedRuleContext(TagsContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitDocblock($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class DescriptionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_description;
	    }

	    public function short_description() : ?Short_descriptionContext
	    {
	    	return $this->getTypedRuleContext(Short_descriptionContext::class, 0);
	    }

	    public function empty_lines() : ?Empty_linesContext
	    {
	    	return $this->getTypedRuleContext(Empty_linesContext::class, 0);
	    }

	    public function long_description() : ?Long_descriptionContext
	    {
	    	return $this->getTypedRuleContext(Long_descriptionContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitDescription($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Short_descriptionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_short_description;
	    }

	    public function lines() : ?LinesContext
	    {
	    	return $this->getTypedRuleContext(LinesContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitShort_description($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Long_descriptionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_long_description;
	    }

	    /**
	     * @return array<LinesContext>|LinesContext|null
	     */
	    public function lines(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(LinesContext::class);
	    	}

	        return $this->getTypedRuleContext(LinesContext::class, $index);
	    }

	    /**
	     * @return array<Empty_linesContext>|Empty_linesContext|null
	     */
	    public function empty_lines(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Empty_linesContext::class);
	    	}

	        return $this->getTypedRuleContext(Empty_linesContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitLong_description($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LinesContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_lines;
	    }

	    /**
	     * @return array<Text_lineContext>|Text_lineContext|null
	     */
	    public function text_line(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Text_lineContext::class);
	    	}

	        return $this->getTypedRuleContext(Text_lineContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitLines($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Text_lineContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_text_line;
	    }

	    public function text_part() : ?Text_partContext
	    {
	    	return $this->getTypedRuleContext(Text_partContext::class, 0);
	    }

	    public function eol() : ?EolContext
	    {
	    	return $this->getTypedRuleContext(EolContext::class, 0);
	    }

	    public function leading_whitespace() : ?Leading_whitespaceContext
	    {
	    	return $this->getTypedRuleContext(Leading_whitespaceContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitText_line($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Text_partContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_text_part;
	    }

	    public function TEXT_PART() : ?TerminalNode
	    {
	        return $this->getToken(DocBlockParser::TEXT_PART, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitText_part($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TagsContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_tags;
	    }

	    /**
	     * @return array<TagContext>|TagContext|null
	     */
	    public function tag(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(TagContext::class);
	    	}

	        return $this->getTypedRuleContext(TagContext::class, $index);
	    }

	    /**
	     * @return array<Empty_linesContext>|Empty_linesContext|null
	     */
	    public function empty_lines(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Empty_linesContext::class);
	    	}

	        return $this->getTypedRuleContext(Empty_linesContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitTags($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TagContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_tag;
	    }

	    public function tag_line() : ?Tag_lineContext
	    {
	    	return $this->getTypedRuleContext(Tag_lineContext::class, 0);
	    }

	    /**
	     * @return array<Text_lineContext>|Text_lineContext|null
	     */
	    public function text_line(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Text_lineContext::class);
	    	}

	        return $this->getTypedRuleContext(Text_lineContext::class, $index);
	    }

	    /**
	     * @return array<Empty_linesContext>|Empty_linesContext|null
	     */
	    public function empty_lines(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Empty_linesContext::class);
	    	}

	        return $this->getTypedRuleContext(Empty_linesContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitTag($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Tag_lineContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_tag_line;
	    }

	    public function tag_part() : ?Tag_partContext
	    {
	    	return $this->getTypedRuleContext(Tag_partContext::class, 0);
	    }

	    public function eol() : ?EolContext
	    {
	    	return $this->getTypedRuleContext(EolContext::class, 0);
	    }

	    public function leading_whitespace() : ?Leading_whitespaceContext
	    {
	    	return $this->getTypedRuleContext(Leading_whitespaceContext::class, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitTag_line($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Tag_partContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_tag_part;
	    }

	    public function TAG_PART() : ?TerminalNode
	    {
	        return $this->getToken(DocBlockParser::TAG_PART, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitTag_part($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Empty_linesContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_empty_lines;
	    }

	    /**
	     * @return array<EolContext>|EolContext|null
	     */
	    public function eol(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(EolContext::class);
	    	}

	        return $this->getTypedRuleContext(EolContext::class, $index);
	    }

	    /**
	     * @return array<Leading_whitespaceContext>|Leading_whitespaceContext|null
	     */
	    public function leading_whitespace(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(Leading_whitespaceContext::class);
	    	}

	        return $this->getTypedRuleContext(Leading_whitespaceContext::class, $index);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitEmpty_lines($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class Leading_whitespaceContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_leading_whitespace;
	    }

	    public function LEADING_WHITESPACE() : ?TerminalNode
	    {
	        return $this->getToken(DocBlockParser::LEADING_WHITESPACE, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitLeading_whitespace($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class EolContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex() : int
		{
		    return DocBlockParser::RULE_eol;
	    }

	    public function EOL() : ?TerminalNode
	    {
	        return $this->getToken(DocBlockParser::EOL, 0);
	    }

		public function accept(ParseTreeVisitor $visitor)
		{
			if ($visitor instanceof DocBlockParserVisitor) {
			    return $visitor->visitEol($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 
}