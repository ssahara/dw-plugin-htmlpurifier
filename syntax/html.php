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
    protected $config = false; // HTML Pretifier configuration object

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_' from class name

        // create HTML Pretifier configuration object
        if (!$this->config) {
            //error_log('created html purifier config for '.$this->mode);
            $this->config = $this->_createHtmlPurifierConfigObject($this->config);
        }
    }

    /**
     * create custum HTML Pretifier configuration object
     * @see http://htmlpurifier.org/docs/enduser-customize.html
     */
    private function _createHtmlPurifierConfigObject($config) {
        global $conf;
        $serializer_dir = $conf['cachedir'].'/htmlpurifier';

        $config = HTMLPurifier_Config::createDefault();
        //$config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
        $config->set('Cache.SerializerPath', $serializer_dir);
        $config->set('Cache.SerializerPermissions', $conf['fmode']);

        $config->set('HTML.DefinitionID', 'plugin_htmlpurifier_html');
        $config->set('HTML.DefinitionRev', 2);

        $config->set('Attr.AllowedFrameTargets', array('_blank','_self'));

        return $config;
    }

    /**
     * Basic functions of syntax plugin component
     */
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

        list($state, $match) = $data;
        if ($format != 'xhtml') return false;

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
            $renderer->doc .= $filter->purify($dirty_html, $this->config);
            $renderer->doc .= '<!-- htmlpurifier end -->'.DOKU_LF;
        } else {
            $method = ($this->getPType() == 'normal') ? 'html' : 'htmlblock';
            $renderer->doc .= $renderer->$method($dirty_html);
        }
        return true;
    }
}
