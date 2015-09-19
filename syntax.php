<?php
/**
 * DokuWiki Plugin DAVCal (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Böhler <dev@aboehler.at>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_abbrlist extends DokuWiki_Syntax_Plugin {
    
    
    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'normal';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 163;
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{abbrlist>[^}]*\}\}',$mode,'plugin_abbrlist');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        global $ID;
        $options = trim(substr($match,11,-2));
        $options = explode(',', $options);
        $data = array(
            'nointernal' => false,
            'sort' => false,
            'acronyms' => array()
        );
        
        foreach($options as $option)
        {
            switch($option)
            {
                case 'nointernal':
                    $data['nointernal'] = true;
                break;

                case 'sort':
                    $data['sort'] = true;
                break;
            }
        }
        
        // Only parse the local config file if we should omit built-in ones
        if($data['nointernal'] === true)
        {
            global $config_cascade;
            if (!is_array($config_cascade['acronyms'])) trigger_error('Missing config cascade for "acronyms"',E_USER_WARNING);
            if (!empty($config_cascade['acronyms']['local']))
            {
                foreach($config_cascade['acronyms']['local'] as $file)
                {
                    if(file_exists($file))
                    {
                        $data['acronyms'] = array_merge($data['acronyms'], confToHash($file));
                    }
                }
            }
        }
        // otherwise, simply use retrieveConfig to fetch all acronyms
        else
        {
            $data['acronyms'] = retrieveConfig('acronyms', 'confToHash');
        }

        // Sort the array, if requested
        if($data['sort'] === true)
        {
            ksort($data['acronyms']);
        }
        return $data;
    }
    
    /**
     * Create output
     */
    function render($format, &$R, $data) {
        if(($format != 'xhtml') && ($format != 'odt')) return false;

        $R->table_open();
        $R->tablethead_open();
        $R->tableheader_open();
        $R->doc .= $this->getLang('key');
        $R->tableheader_close();
        $R->tableheader_open();
        $R->doc .= $this->getLang('value');
        $R->tableheader_close();
        $R->tablethead_close();
        foreach($data['acronyms'] as $key => $val)
        {
            $R->tablerow_open();
            $R->tablecell_open();
            $R->doc .= $key;
            $R->tablecell_close();
            $R->tablecell_open();
            $R->doc .= $val;
            $R->tablecell_close();
            $R->tablerow_close();
        }
        $R->table_close();
     
    }


   
}

// vim:ts=4:sw=4:et:enc=utf-8:
