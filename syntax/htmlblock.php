<?php
/**
 * HTML Purifier plugin for DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Satoshi Sahara <sahara.satoshi@gmail.com>
 */

require_once(dirname(__FILE__).'/html.php');

class syntax_plugin_htmlpurifier_htmlblock extends Syntax_Plugin_htmlpurifier_html {

    protected $entry_pattern    = '<HTML>(?=.*?</HTML>)';
    protected $exit_pattern     = '</HTML>';

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 189; } // = Doku_Parser_Mode_html -1

}
