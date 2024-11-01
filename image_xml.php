<?php

$iPostID = $_GET["id"];

require($subdir.'../../../wp-blog-header.php');

if (!empty($iPostID)) {
	$arrImages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );
    if($arrImages) {
		// bubble sort images to match the order in wp admin.
		// many thanks to John Crenshaw -> http://wphackr.com/get-images-attached-to-post/
        $arrKeys = array_keys($arrImages);
		foreach($arrImages as $oImage) {
			$arrNewImages[] = $oImage;
		}
		for($i = 0; $i < sizeof($arrNewImages) - 1; $i++) {
			for($j = 0; $j < sizeof($arrNewImages) - 1; $j++) {
				if((int)$arrNewImages[$j]->menu_order > (int)$arrNewImages[$j + 1]->menu_order) {
					$oTemp = $arrNewImages[$j];
					$arrNewImages[$j] = $arrNewImages[$j + 1];						$arrNewImages[$j + 1] = $oTemp;
				}
			}
		}
		$arrKeys = array();
		foreach($arrNewImages as $oNewImage) {
			$arrKeys[] = $oNewImage->ID;
		}
	}
	header("content-type:text/xml;charset=utf-8");
	echo "<SLGALLERY>";
	for ($i=0;$i<count($arrKeys);$i++) {
		$iNum = $arrKeys[$i];
		echo "<IMAGE Url=\"".wp_get_attachment_url($iNum)."\" Titre=\"".$arrImages[$iNum]->post_title."\" Descriptif=\"".$arrImages[$iNum]->post_content."\" />";
	}
	echo "</SLGALLERY>";
}

?>
