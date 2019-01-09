<?php

class imager extends mysqlv{
	var $dbLink = NULL;
	
	public function dbCon($host,$user,$pass,$db){
		$this->dbLink = parent::connect($host, $user, $pass, $db);
	}	
	
	/*
		Function Name: getGalleryImages 
	*/
	
	public function getGalleryImages()
	{
		$galleryImages = array();
		$g = 0;
		$getImages = "SELECT imgid, img_tags, img_name, img_description, img_path FROM tblgallery WHERE img_isvisible = 'yes'";
		$qry = parent::query($getImages, $this->dbLink) or die(mysql_error()."Error getting gallery images");
		while($images = parent::fetch($qry)):
			$galleryImages[$g]['imgid'] = $images['imgid'];
			$galleryImages[$g]['img_tags'] = $images['img_tags'];
			$galleryImages[$g]['img_name'] = $images['img_name'];
			$galleryImages[$g]['img_description'] = $images['img_description'];
			$galleryImages[$g]['img_path'] = $images['img_path'];
			$galleryImages[$g]['img_isvisible'] = $images['img_isvisible'];
			$g++;
		endwhile;
		return $galleryImages;
	}
	
	public function getConfImages($offset, $upperlimit, $total_pages, $limit)
	{
		$galleryImages = array();
		$g = 0;
		
		if ($total_pages > $limit):
			$getImages = "SELECT imgid, img_tags, img_name, img_description, img_path FROM tblconfphotos LIMIT $offset, $upperlimit";
		else:
			$getImages = "SELECT imgid, img_tags, img_name, img_description, img_path FROM tblconfphotos";
		endif;
			
		$qry = parent::query($getImages, $this->dbLink) or die(mysql_error()."Error getting gallery images");
		while($images = parent::fetch($qry)):
			$galleryImages[$g]['imgid'] = $images['imgid'];
			$galleryImages[$g]['img_tags'] = $images['img_tags'];
			$galleryImages[$g]['img_name'] = $images['img_name'];
			$galleryImages[$g]['img_description'] = $images['img_description'];
			$galleryImages[$g]['img_path'] = $images['img_path'];
			$galleryImages[$g]['img_isvisible'] = $images['img_isvisible'];
			$g++;
		endwhile;
		return $galleryImages;
	}
	
	
	/*
	Function Name: scale_image
	Parameters: $imageURL - path where the image resides
				$image - the image file
				$width - defined width
				$height - defined height
	*/
	
	public function scale_image($imageURL, $image, $width, $height)
	{
		$newImage = '<img src="'.$imageURL.'/'.$image.'" width="'.$width.'" height="'.$height.'" /> ';
		return $newImage;
	}

}


/**/