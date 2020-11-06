lexer grammar DocBlockLexer;

EOL: '\r'?'\n'|'\n'?'\r';

LEADING_WHITESPACE: (([ \t]*'*'[ \t]*)|([ \t]+)) -> mode(MODE_LINE);

AT1: '@' -> more, mode(MODE_TAG);

OTHER: ~[ \t*\r\n@] -> more, mode(MODE_TEXT);

EOL1: EOL -> type(EOL);


mode MODE_LINE;

AT2: '@' -> more, mode(MODE_TAG);

TEXT: ~[\n\r@] -> more, mode(MODE_TEXT);

EOL2: EOL -> type(EOL), mode(DEFAULT_MODE);


mode MODE_TAG;

TAG_PART: (~[\r\n])+ ;

EOL3: EOL -> type(EOL), mode(DEFAULT_MODE);


mode MODE_TEXT;

TEXT_PART: (~[\r\n])+;

EOL4: EOL -> type(EOL), mode(DEFAULT_MODE);


