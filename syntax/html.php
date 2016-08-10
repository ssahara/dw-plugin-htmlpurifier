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

    protected $entry_pattern    = '<html>(?=.*?</html>)';
    protected $exit_pattern     = '</html>';
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
        $this->Lexer->addEntryPattern($this->entry_pattern, $mode, $this->mode);
    }

    function postConnect() {
        $this->Lexer->addExitPattern($this->exit_pattern, $this->mode);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
            case DOKU_LEXER_ENTER:
                return array($state,'');

            case DOKU_LEXER_UNMATCHED:
                return array($state, $match);

            case DOKU_LEXER_EXIT:
                return array($state, '');
        }
        return false;
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {
        global $conf;

        list($state, $dirty_html) = $data;
        if (empty($dirty_html) || ($format != 'xhtml'))  return false;

        $filter = $this->loadHelper($this->getPluginName());

        // customize HTML Purifier's behavior
        $filter->config->set('Attr.AllowedFrameTargets', array('_blank','_self'));

        if ($conf['htmlok']) {
            $renderer->doc .= $filter->purify($dirty_html);
        } else {
            $method = ($this->getPType() == 'normal') ? 'html' : 'htmlblock';
            $renderer->doc .= $renderer->$method($dirty_html);
        }
        return true;
    }
}
