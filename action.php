<?php

if(!defined('DOKU_INC')) die();

class action_plugin_abbrlist extends DokuWiki_Action_Plugin {
    
  // Register our hooks 
  function register(&$controller) {
    $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, 'handle_parser_cache_use');    
  }
  
  function handle_parser_cache_use(&$event, $param)
  {
      global $ID;
      $cache = &$event->data;
      if(!isset($cache->page)) return;
      //purge only xhtml cache
      if($cache->mode != "xhtml") return;
      
      $abbrMeta = p_get_metadata($ID, 'abbrlist');
      if(!$abbrMeta)
        return;
      
      $cache->depends['files'][] = DOKU_INC + 'conf/acronyms.conf';
      $cache->depends['files'][] = DOKU_INC + 'conf/acronyms.local.conf';
      
  }
  
}