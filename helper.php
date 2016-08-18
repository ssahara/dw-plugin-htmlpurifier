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

    protected $purifier = false;

    function __construct() {
        // create instance of HTML Purifier
        if (!$this->purifier) {
            //error_log("purifier object instantiated!");
            $this->purifier = new HTMLPurifier();
        }
    }

    /**
     * Purify HTML
     * @param string $dirty_html  String HTML to purify
     * @param mixed  $config      HTML Purifier configuration object
     * @return string
     */
    function purify($dirty_html, $config=null) {

        if ($config == null) {
            global $conf;
            $serializer_dir = $conf['cachedir'].'/htmlpurifier';
            $config = HTMLPurifier_Config::createDefault();
            //$config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
            $config->set('Cache.SerializerPath', $serializer_dir);
            $config->set('Cache.SerializerPermissions', $conf['fmode']);
        }
        return $this->purifier->purify($dirty_html, $config);
    }

}
