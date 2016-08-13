<?php
/**
 * HTML Purifier plugin for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Satoshi Sahara <sahara.satoshi@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_htmlpurifier_html extends DokuWiki_Syntax_Plugin {

    protected $match_pattern    = '<html\b[^>\r\n]*?>.*?</html>';
    protected $mode;

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_' from class name
    }

    function getType()  { return 'protected'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 189; } // = Doku_Parser_Mode_html -1

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->match_pattern, $mode, $this->mode);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
        return array($state, $match);
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        global $conf;

        if ($format != 'xhtml') return false;

        list($state, $match) = $data;
        $purify = true;

        $matches = explode('>', $match, 2);
        if (strpos($matches[0], 'nopurify') !== false) {
            $purify = false;
        }
        $dirty_html = trim(substr($matches[1], 0, -7));
        if (empty($dirty_html)) return false;


        if ($conf['htmlok'] && $purify) {
            // load HTML Purifier
            $filter = $this->loadHelper($this->getPluginName());

            $renderer->doc .= '<!-- htmlpurifier start -->'.DOKU_LF;
            $renderer->doc .= $filter->purify($dirty_html);
            $renderer->doc .= '<!-- htmlpurifier end -->'.DOKU_LF;
        } else {
            $method = ($this->getPType() == 'normal') ? 'html' : 'htmlblock';
            $renderer->doc .= $renderer->$method($dirty_html);
        }
        return true;
    }
}
