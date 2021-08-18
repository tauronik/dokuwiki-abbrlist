<?php
/**
 * DokuWiki Plugin abbrlist (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version 2021-08-12
 * @author  Michael Schatz <m.schatz@tauronik.de>
 *
 * @author  (Original by) Andreas Böhler <dev@aboehler.at>
 */


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
    function handle($match, $state, $pos, Doku_Handler $handler){
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
    function render($format, Doku_Renderer $R, $data) {
        if($format == 'metadata')
        {
          if(!isset($renderer->meta['abbrlist']))
            p_set_metadata($ID, array('abbrlist' => array()));
          return true;
        }
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

