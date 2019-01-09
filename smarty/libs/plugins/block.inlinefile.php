<?php
/**
 * Smarty plugin to inline file contents
 *
 * @package Smarty
 * @subpackage PluginsBlock
 */

/**
 * Smarty {inlinefile}{/inlinefile} block plugin
 *
 * Type:     block function<br>
 * Name:     inlinefile<br>
 * Purpose:  inline file contents<br>
 * Params:
 * <pre>
 * - style         - string (email)
 * - indent        - integer (0)
 * - wrap          - integer (80)
 * - wrap_char     - string ("\n")
 * - indent_char   - string (" ")
 * - wrap_boundary - boolean (true)
 * </pre>
 *
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Function &$smarty  template object
 * @param boolean                  &$repeat  repeat flag
 * @return string CSS/JS inline
 * @author Ardian Rifhardy 
 */
function smarty_block_inlinefile($params, $content, &$smarty, &$repeat)
{
    // only output on the closing tag
     if(!$repeat){
        if (isset($content)) {
            switch ($params['type']) {
                case 'css':
                    $url = preg_match('/href="(.+)"/', $content, $match);
            
                    if ($match) {
                        $info = $match[1];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $info)) {
                            $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $info);
                            return '<style type="text/css" rel="stylesheet">' . str_replace(array("\r", "\n"), '', $file) . '</style>';
                        } else {
                            return $content;
                        }
                    }
                    break;
                case 'js':
                    $url = preg_match('/src="(.+)"/', $content, $match);
            
                    if ($match) {
                        $info = $match[1];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $info)) {
                            $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $info);
                            return '<script type="text/javscript">' . str_replace(array("\r", "\n"), '', $file) . '</script>';
                        } else {
                            return $content;
                        }
                    }
                    break;
            }
            return $content;
        }
    } 
}

?>