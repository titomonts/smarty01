<?php
function smarty_function_reviews($params, &$smarty)
{
	global $website,$data;
	
	/* SET XML Folder for Reviews */
	$xpath = MAIN_FOLDER."/villa-xml/".$data['theme']."/";
	if(!is_dir($xpath))
		mkdir($xpath,0775);
	/* End SET XML Folder for Reviews */
	
    if (array_key_exists('villa_id', $params)) {
        if (strpos($params['villa_id'], ',') > -1) {
            $villa_id = explode(',', $params['villa_id']);
        } else {
            $villa_id[0] = $params['villa_id'];
        }
    } else {
        $villa_id[0] = $data['villa_id'];
    }
    
    $rawiswar = '';
    for($i = 0; $i < count($villa_id); $i++) {
        if( !file_exists( $xpath.$villa_id[$i].'_reviews.xml' ) ):
            $request = "strVillaID=".$villa_id[$i];
            $xml = $website->curly_tops('getVillaReviews',$request,TRUE,TRUE,$villa_id[$i].'_reviews',$xpath,'prod');
        endif;
        $reviews = array();
        $rc = 0;

        /* PARSE DATA FROM XML */
        $file = fopen( $xpath.$villa_id[$i].'_reviews.xml', 'r' );

        while( $xd = fread( $file, 65535 ) ):
            $xd = trim($xd);
            $xd = preg_replace('/\s+/', ' ', $xd);
            $xd = str_replace('&nbsp;', ' ', $xd);
            $rawiswar .= $xd; 
        endwhile;
        fclose( $file );
    }

	preg_match_all( "/\<REVIEW\>(.*?)\<\/REVIEW\>/s", $rawiswar, $vblocks );

	foreach( $vblocks[1] as $vblock ):
		preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $status );
		preg_match_all( "/\<Name\>(.*?)\<\/Name\>/", $vblock, $name );
		
		if($status[1][0] == 'Approved' && $name[1][0] != 'Staff'):
			
			preg_match_all( "/\<SDate\>(.*?)\<\/SDate\>/", $vblock, $sdate );
			$reviews[$rc]['SDate'] = $sdate[1][0];
			
			preg_match_all( "/\<EDate\>(.*?)\<\/EDate\>/", $vblock, $edate );
			$reviews[$rc]['EDate'] = $edate[1][0];
			
			preg_match_all( "/\<ReviewByName\>(.*?)\<\/ReviewByName\>/", $vblock, $reviewbyname );
			$reviews[$rc]['ReviewByName'] = $reviewbyname[1][0];
			
			preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $rstatus );
			$reviews[$rc]['ApprStatusID'] = $rstatus[1][0];
			
			preg_match_all( "/\<ROverallComments\>(.*?)\<\/ROverallComments\>/", $vblock, $rcomments );
			if( array_key_exists(0,$rcomments[1]) ):
				$reviews[$rc]['ROverallComments'] = $rcomments[1][0];
			else:
				$reviews[$rc]['ROverallComments'] = '';	
			endif;
			
			$rc++;
		endif;
	endforeach;
	/* END PARSE DATA FROM XML */

	/* DISPLAY ARRAY RESULT */
	$hide_this = array();
	if( array_key_exists('hide_this_year', $params) ):
		if( strpos($params['hide_this_year'], ',') !== FALSE ):
			$hide_this = explode(',',$params['hide_this_year']);
		else:
			$hide_this[0] = $params['hide_this_year'];
		endif;
	endif;
	$sr = sizeof($reviews); 
	$result = '<ul class="slides">';
	if( $sr > 0 ):
        if (array_key_exists('limit', $params)) {$sr = $sr > intval($params['limit']) ? intval($params['limit']) : $sr;}
		for( $n = 0; $n < $sr; $n++ ): 
			$reviews = $website->subval_sort($reviews,'SDate');
			if( $reviews[$n]['ROverallComments'] != '' ):
                
                $filter_this = date('Y', strtotime($reviews[$n]['EDate']));
                if( sizeof($hide_this) <= 0 || !in_array($filter_this,$hide_this) ):
    
                    $result .='<li class="gsrev"><div class="right">';

                    $result_name = '<span class="rname">'.$reviews[$n]['ReviewByName'].'</span>';
                    $result_content = '<p>'.$reviews[$n]['ROverallComments'].'</p>';
                    $result_default = '<p>'.$result_name.'<br />'.$reviews[$n]['ROverallComments'].'</p>';


                    if( array_key_exists('SDate',$reviews[$n]) ):
                        if( sizeof($hide_this) <= 0 ):
                            $result_date = '<p class="gr-date">'.date("F jS, Y", strtotime($reviews[$n]['SDate'].' + 1 day')).' - '.date("F jS, Y", strtotime($reviews[$n]['EDate'].' + 1 day')).'</p>';
                        else:
                            $filter_this = date('Y', strtotime($reviews[$n]['EDate']));
                            if( !in_array($filter_this,$hide_this) ):
                                $result_date = '<p class="gr-date">'.date("F jS, Y", strtotime($reviews[$n]['SDate'].' + 1 day')).' - '.date("F jS, Y", strtotime($reviews[$n]['EDate'].' + 1 day')).'</p>';
                            endif;
                        endif;					
                    endif;

                    if( array_key_exists('order', $params) ):
                        if( strpos($params['order'], ',') !== FALSE ):
                            $review_order = explode(',',$params['order']);

                            for( $x = 0; $x < sizeof($review_order); $x++ ):
                                if( strpos($review_order[$x], 'c') !== FALSE ):
                                    $result .= $result_content;
                                elseif( strpos($review_order[$x], 'd') !== FALSE ):
                                    $result .= $result_date;
                                else:
                                    $result .= $result_name;
                                endif;
                            endfor;

                        else:
                            $result .= $result_default . $result_date;
                        endif;
                    else:
                        $result .= $result_default . $result_date;
                    endif;

                    $result .='</div></li>';
                endif;
			endif;
		endfor;
	else:
		$result .= '<li class="gsrev">No reviews yet at the moment</li>';	
	endif;
	$result .= '</ul>';
	/* END DISPLAY ARRAY RESULT */
	
	$smarty->assign('reviews',$result);
}