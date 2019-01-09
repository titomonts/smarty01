<?php
function smarty_function_prevnext($params, &$smarty)
{
	global $page_structure, $data; 
	
	for($ps=0; $ps<sizeof($page_structure); $ps++):
		//if( $params['page'] == $page_structure[$ps]['page_identifier'] && ( $data['parent'] == $params['page'] || ( $data['parent'] != $params['page'] && $page_structure[$ps]['parent'] == $data['parent'] ) || !$page_structure[$ps]['parent'] ) ):
        
		if( $params['page'] == $page_structure[$ps]['page_identifier'] && ( $data['parent'] == $params['page'] || ( $data['parent'] != $params['page'] && $page_structure[$ps]['parent'] == $data['parent'] ) || (!$page_structure[$ps]['parent'] && $page_structure[$ps]['origin'] == $page_structure[$ps]['page_identifier']) ) ):
    
			$prev = $page_structure[$ps-1]['page_identifier'];
			$prev_label = $page_structure[$ps-1]['label'];
			
			$next = $page_structure[$ps+1]['page_identifier'];
			$next_label = $page_structure[$ps+1]['label'];
		
            if( array_key_exists('children',$page_structure[$ps-1]) ):
                $size_c = sizeof($page_structure[$ps-1]['children']);
                $prev = $page_structure[$ps-1]['children'][$size_c-1]['page_identifier'];
                if( array_key_exists('parent',$page_structure[$ps-1]['children'][$size_c-1]) ):
                    $prev = $page_structure[$ps-1]['children'][$size_c-1]['parent'].'/'.$page_structure[$ps-1]['children'][$size_c-1]['page_identifier'];
                endif;
                $prev_label = $page_structure[$ps-1]['children'][$size_c-1]['label'];
            endif;
		
            if( array_key_exists('children',$page_structure[$ps+1]) ):
                $next = $page_structure[$ps+1]['children'][0]['page_identifier'];
                if( array_key_exists('parent',$page_structure[$ps+1]['children'][0]) ):
                    $next = $page_structure[$ps+1]['children'][0]['parent'].'/'.$page_structure[$ps+1]['children'][0]['page_identifier'];
                endif;
                $next_label = $page_structure[$ps+1]['children'][0]['label'];
            endif;
		endif;
		
		if( array_key_exists('children',$page_structure[$ps]) ):
			for( $sp=0; $sp<sizeof($page_structure[$ps]['children']); $sp++ ):
				if( $params['page'] == $page_structure[$ps]['children'][$sp]['page_identifier'] && ( $data['parent'] == $params['page'] || ( $data['parent'] != $params['page'] && $page_structure[$ps]['children'][$sp]['parent'] == $data['parent']) || !$page_structure[$ps]['children'][$sp]['parent'] ) ):
					if( $page_structure[$ps]['children'][$sp-1]['page_identifier'] == '' ):
						$prev = $page_structure[$ps-1]['page_identifier'];
						$prev_label = $page_structure[$ps-1]['label'];
		
                        if( array_key_exists('children',$page_structure[$ps-1]) ):
                            $size_c = sizeof($page_structure[$ps-1]['children']);
                            $prev = $page_structure[$ps-1]['children'][$size_c-1]['page_identifier'];
                            if( array_key_exists('parent',$page_structure[$ps-1]['children'][$size_c-1]) ):
                                $prev = $page_structure[$ps-1]['children'][$size_c-1]['parent'].'/'.$page_structure[$ps-1]['children'][$size_c-1]['page_identifier'];
                            endif;
                            $prev_label = $page_structure[$ps-1]['children'][$size_c-1]['label'];
                        endif;
					else:
						if( array_key_exists('parent',$page_structure[$ps]['children'][$sp-1]) ):
							$prev = $page_structure[$ps]['children'][$sp-1]['parent'].'/'.$page_structure[$ps]['children'][$sp-1]['page_identifier'];
						else:
							$prev = $page_structure[$ps]['children'][$sp-1]['page_identifier'];
						endif;
						$prev_label = $page_structure[$ps]['children'][$sp-1]['label'];
					endif;
					
					if( $page_structure[$ps]['children'][$sp+1]['page_identifier'] == '' ):
						$next = $page_structure[$ps+1]['page_identifier'];
						$next_label = $page_structure[$ps+1]['label'];
		
                        if( array_key_exists('children',$page_structure[$ps+1]) ):
                            $next = $page_structure[$ps+1]['children'][0]['page_identifier'];
                            if( array_key_exists('parent',$page_structure[$ps+1]['children'][0]) && $page_structure[$ps+1]['children'][0]['parent'] != $page_structure[$ps+1]['children'][0]['page_identifier'] ):
                                $next = $page_structure[$ps+1]['children'][0]['parent'].'/'.$page_structure[$ps+1]['children'][0]['page_identifier'];
                            endif;
                            $next_label = $page_structure[$ps+1]['children'][0]['label'];
                        endif;
					else:
						if( array_key_exists('parent',$page_structure[$ps]['children'][$sp+1]) ):
							$next = $page_structure[$ps]['children'][$sp+1]['parent'].'/'.$page_structure[$ps]['children'][$sp+1]['page_identifier'];
						else:
							$next = $page_structure[$ps]['children'][$sp+1]['page_identifier'];
						endif;
						$next_label = $page_structure[$ps]['children'][$sp+1]['label'];
					endif;
				endif;
			endfor;
		endif;
	endfor;
	
	$smarty->assign('prev', $prev);
	$smarty->assign('prev_label', $prev_label);
	$smarty->assign('next', $next);
	$smarty->assign('next_label', $next_label);
}