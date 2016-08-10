<?php
/**
 * HTML Purifier plugin for DokuWiki
 *
 * HTML Purifier is a standards-compliant HTML filter library written in PHP.
 * The library is licensed under the LGPL v2.1+.
 * @see http://htmlpurifier.org/
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Satoshi Sahara <sahara.satoshi@gmail.com>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(DOKU_PLUGIN.'/htmlpurifier/library/HTMLPurifier.auto.php');

class helper_plugin_htmlpurifier extends DokuWiki_Plugin {

    // Return false to prevent DokuWiki reusing instances of the plugin
    function isSingleton() {
        return false;
    }

    public $config; // HTML Pretifier configuration object

    function __construct() {
        global $conf;

        // create HTML Pretifier configuration object
        $this->config = HTMLPurifier_Config::createDefault();
        // $this->config->set('Core.Encoding', 'UTF-8');
        // $this->config->set('HTML.Doctype', 'XHTML 1.0 Transitional');

        // prepare cache directory for HTMLPurifier/DefinitionCache/Serializer
        $serializer_dir = $conf['cachedir'].'/htmlpurifier';
        io_mkdir_p($serializer_dir);
        $this->config->set('Cache.SerializerPath', $serializer_dir);

        //$this->config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
    }

    function purify($dirty_html) {
        $purifier = new HTMLPurifier($this->config);
        return $purifier->purify($dirty_html);
    }

}
