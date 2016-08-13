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
    public    $config = false; // HTML Pretifier configuration object

    function __construct() {
        global $conf;

        // prepare cache directory for HTMLPurifier/DefinitionCache/Serializer
        $serializer_dir = $conf['cachedir'].'/htmlpurifier';
        io_mkdir_p($serializer_dir.'/CSS');
        io_mkdir_p($serializer_dir.'/HTML');
        io_mkdir_p($serializer_dir.'/URI');

        // create HTML Pretifier configuration object
        if (!$this->config) {
            error_log("purifier config object created!");
            $this->config = HTMLPurifier_Config::createDefault();
            $this->config->set('Cache.SerializerPath', $serializer_dir);
            $this->config->set('Cache.SerializerPermissions', $conf['fmode']);
            $this->config->set('Attr.AllowedFrameTargets', array('_blank','_self'));
        }

        // create instance of HTML Purifier
        if (!$this->purifier) {
            error_log("purifier object instantiated!");
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
            return $this->purifier->purify($dirty_html, $this->config);
        }
        return $this->purifier->purify($dirty_html, $config);
    }

}
