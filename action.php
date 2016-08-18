<?php
/**
 * HTML Purifier plugin for DokuWiki; Action component
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Satoshi Sahara <sahara.satoshi@gmail.com>
 */
if(!defined('DOKU_INC')) die();

require_once(DOKU_PLUGIN.'/htmlpurifier/library/HTMLPurifier.auto.php');

class action_plugin_htmlpurifier extends DokuWiki_Action_Plugin {

    // register hook
    public function register(Doku_Event_Handler $controller) {
       $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, '_prepareCacheDir');
    }

    /**
     * prepare cache directory for HTMLPurifier/DefinitionCache/Serializer
     */
    function _prepareCacheDir(Doku_Event $event, $params) {
        global $conf;
        $serializer_dir = $conf['cachedir'].'/htmlpurifier';
        //io_mkdir_p($serializer_dir);

        // sub directory for each type of definition
        io_mkdir_p($serializer_dir.'/CSS');
        io_mkdir_p($serializer_dir.'/HTML');
        io_mkdir_p($serializer_dir.'/URI');
    }

}
