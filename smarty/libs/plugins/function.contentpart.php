<?php
function smarty_function_contentpart($params, &$smarty)
{
	error_reporting(E_ALL ^ E_NOTICE);
	
    if(isset($params['content'])):
        $params['content'] = str_replace('  ', ' ', $params['content']);
        $params['content'] = str_replace('<br>', '<br />', $params['content']);
        $params['content'] = str_replace('&lt;br&gt;', '&lt;br /&gt;', $params['content']);
        if(isset($params['strip'])):
            if(is_array($params['strip'])):
                for($i=0; $i<sizeof($params['strip']); $i++):
                    $params['content'] = str_replace($params['strip'][$i], '', $params['content']);
                endfor;
            else:
                $params['content'] = str_replace($params['strip'], '', $params['content']);
            endif;
        endif;
    
        if(isset($params['remove'])):
            if(is_array($params['remove'])):
                $params['content'] = preg_replace($params['remove'], '', $params['content']);
            else:
                $params['content'] = str_replace($params['remove'], '', $params['content']);
            endif; 
        endif;
    endif;
    
    $params['content'] = trim(preg_replace("/[\n\r]/", "", $params['content']));
    
    if(isset($params['explode'])):
        $part = explode($params['explode'], $params['content']);
    endif;
    
    if(isset($params['regex'])):
        if($params['regex'] == 'default'):
            $value = preg_match_all('/<p>(.*?)<\/p>/s', $params['content'], $part);
        else:
            $value = preg_match_all($params['regex'], $params['content'], $part);
        endif;
        $part = $part[0]; 
    endif;
    
	$smarty->assign('contentCount', sizeof($part) );
	$smarty->assign('contentPart', $part );
}