<?php
function smarty_function_get_album($params, &$smarty)
{
	global $mysqli, $website, $data;
	$mysqli = $website->connect();
	$where = isset($params['tag'])?" AND gal_tag = '".$params['tag']."'":"";
	$where .= $data['complex']=='yes'?" AND gal_vid = '".$params['vid']."'":"";

	$gqry = $mysqli->query("SELECT * FROM smarty_gallery WHERE gal_sid = ".(int)$data['site_id']." ".$where." ORDER BY gal_sort ASC") or die($mysqli->error);
	$gallery = array();
	$g = 0;
	while( $row = $gqry->fetch_assoc() ):
		$gallery[$g]['path'] = $data['complex']=='no'?'/gallery/'.$params['vid']:'/gallery/'.$data['site_id'].'/'.$params['vid'];
		$gallery[$g]['title'] = $row['gal_description'];
		$gallery[$g]['image_src'] = $row['gal_filename'];
		$gallery[$g]['thumbnail_src'] = $row['gal_thumbnail'];
		$gallery[$g]['alt'] = $row['gal_description'];
		$gallery[$g]['villaID'] = $row['gal_vid'];
		$gallery[$g]['gal_thumb_height'] = $row['gal_thumb_height'];
		$gallery[$g]['gal_thumb_width'] = $row['gal_thumb_width'];
		$gallery[$g]['tag'] = $row['gal_tag'];
		$g++;
	endwhile;
	
	$pgallery = $data['gallery_image_per_page']>0?array_chunk($gallery,$data['gallery_image_per_page']):array_chunk($gallery,sizeof($gallery));
	$data['num_pages'] = ($data['gallery_image_per_page'] > 0) ? ceil(sizeof($gallery)/$data['gallery_image_per_page']) : 0;
	$data['nggpage'] = $data['nggpage']<=0?0:$data['nggpage'];
	$data['cur_page'] = $data['nggpage']+1;
	$smarty->assign("gallery",$pgallery[ $data['nggpage'] ]);
}