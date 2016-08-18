<?php
/**
 * HTML Purifier plugin for DokuWiki; allow some raw html tags
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Satoshi Sahara <sahara.satoshi@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_htmlpurifier_element extends DokuWiki_Syntax_Plugin {

    protected $pattern_iframe = '<iframe\b[^>\r\n]*?>.*?</iframe>';
    protected $pattern_object = '<object\b[^>\r\n]*?>.*?</object>';
    protected $pattern_img    = '<img\b[^>\r\n]*?>';

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

        $config->set('HTML.DefinitionID', 'plugin_htmlpurifier_element');
        $config->set('HTML.DefinitionRev', 3);

        $whitelist = 'iframe[src|name|height|width|style|title],'
                    .'img[src|height|width|style|title|alt],'
                    .'object[data|type|height|width|style|title],'
                    .'param[name|value]';
        $config->set('HTML.Allowed', $whitelist);

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            // add <iframe> element
            $elem = $def->addElement(
                'iframe',  // element name
                'Block',   // content set: Inline|Block|false
                'Flow',    // allowed children content model: Empty|Inline|Flow
                'Common',  // common attribute collection: Common|Core|I18N
                array(     // unique attributes
                    'src'    => 'URI',
                    'name'   => 'CDATA',
                    'height' => 'Length',
                    'width'  => 'Length',
                )
            );
            $elem->excludes = array('iframe' => true); // prevents from being nested

            // add <object> element
            $elem = $def->addElement(
                'object',
                'Block',
                'Flow',
                'Common',
                array(
                    'data'   => 'URI',
                    'type'   => 'CDATA',
                    'height' => 'Length',
                    'width'  => 'Length',
                )
            );
            $elem->excludes = array('object' => true); // prevents from being nested

            // add <param> element
            $elem = $def->addElement(
                'param',
                false,
                'Empty',
                'Common',
                array(
                    'name*' => 'CDATA',
                    'value' => 'CDATA',
                )
            );
        }
        return $config;
    }

    /**
     * Basic functions of syntax plugin component
     */
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 189; } // = Doku_Parser_Mode_html -1

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->pattern_iframe, $mode, $this->mode);
        $this->Lexer->addSpecialPattern($this->pattern_object, $mode, $this->mode);
        $this->Lexer->addSpecialPattern($this->pattern_img, $mode, $this->mode);
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

        list($state, $dirty_html) = $data;
        if (empty($dirty_html) || ($format != 'xhtml'))  return false;

        if ($conf['htmlok']) {
            // load HTML Purifier
            $filter = $this->loadHelper($this->getPluginName());

            $renderer->doc .= '<!-- htmlpurifier_element start -->'.DOKU_LF;
            $renderer->doc .= $filter->purify($dirty_html, $this->config);
            $renderer->doc .= '<!-- htmlpurifier_element end -->'.DOKU_LF;
        } else {
            $method = ($this->getPType() == 'normal') ? 'html' : 'htmlblock';
            $renderer->doc .= $renderer->$method($dirty_html);
        }
        return true;
    }
}
