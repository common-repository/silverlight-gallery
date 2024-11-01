<?php
/*
Plugin Name: Silverlight Gallery
Plugin URI: http://www.flashxpress.net/ressources-silverlight-blend/plugin-galerie-wordpress-en-silverlight/
Description: Add a Silverlight© gallery to your wordpress posts, witch uses the post attached images
Version: 1.0
Author: Regart.net
Author URI: http://www.regart.net

	Copyright (c) 2009 regart.net (http://www.regart.net) & Stéréosuper.fr (http://www.stereosuper.fr)
	Silverlight Gallery is released under the GNU General Public
	License: http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org). WordPress is
	free software; you can redistribute it and/or modify it under the
	terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your
	option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	General Public License for more details.

	For a copy of the GNU General Public License, write to:

	Free Software Foundation, Inc.
	59 Temple Place, Suite 330
	Boston, MA  02111-1307
	USA

	You can also view a copy of the HTML version of the GNU General
	Public License at http://www.gnu.org/copyleft/gpl.html

Many thanks to Tim Heuer (http://timheuer.com/blog/) for his helpfull silverlight plugin "Silverlight for WordPress" (http://timheuer.com/silverlight-for-wordpress)
*/

define("SLGALLERY_META_START", "[sl-gallery:");
define("SLGALLERY_META_END", "]");
define("SLGALLERY_TARGET", "<div class=\"silverlightControlHost\"><object data=\"data:application/x-silverlight-2,\" type=\"application/x-silverlight-2\" width=\"###WIDTH###\" height=\"###HEIGHT###\" style='outline:none'>\n<param name=\"source\" value=\"".urlPlugins("silverlight-gallery")."/ClientBin/pluginSL.xap\"/>\n<param name=\"background\" value=\"###BGCOLOR###\" />\n<param name=\"windowless\" value=\"###BGMODE###\" />\n<param name=\"minRuntimeVersion\" value=\"###MINVER###\" />\n<param name=\"initParams\" value=\"GalleryUrl=".urlPlugins("silverlight-gallery")."/image_xml.php?id=###POSTID###,\n\t leftArrow=###LEFTARROW###, \n\trightArrow=###RIGHTARROW###, \n\tpageNumbers=###PAGENUMBERS###, \n\tthumbnails=###THUMBNAILS###, \n\tfill=###FILL######OTHERPARAMS###\" />\n<param name=\"autoupgrade\" value=\"true\" />\n<param name=\"enableHtmlAccess\" value=\"true\" />###PRELOADER######ALTERNATE###</object></div>");

function slgallery_the_content($content) {
	global $post;
	$output = slgallery_output($content);
	if ($output == "") return ($content);
	$found_pos = strpos($content, SLGALLERY_META_START);
	$embedded = substr($content, 0, $found_pos);
	$output = $embedded.$output;
	$output .= substr($content, strpos($content, SLGALLERY_META_END, $found_pos)+1);
    return ($output);
}

function slgallery_output($content) {
	global $post;
	$found_pos = strpos($content, SLGALLERY_META_START);
	if($found_pos === false) return null;
	$meta = explode(",", trim(substr($content, $found_pos+strlen(SLGALLERY_META_START), (strpos($content, SLGALLERY_META_END, $found_pos) - ($found_pos+strlen(SLGALLERY_META_START))))));
	$output = SLGALLERY_TARGET;
	$paramHolder = SLGALLERY_INITPARAMS;
	// récupérer les valeurs
	$width = "";
	$height = "";
	$leftArrow = "";
	$rightArrow = "";
	$pageNumbers = "";
	$thumbnails = "";
	$preloader = "";
	$fill = "";
	$bgColor = "";
	$bgMode = "";
	$otherParams = "";
	for($i=0;$i<count($meta);$i++) {
		$meta[$i] = strtolower(trim($meta[$i]));
		if (is_numeric(strpos($meta[$i],"width="))) {
			$width = substr($meta[$i],strrpos($meta[$i],"=")+1);
		} elseif (is_numeric(strpos($meta[$i],"height="))) {
			$height = substr($meta[$i],strrpos($meta[$i],"=")+1);
		} elseif (is_numeric(strpos($meta[$i],"leftarrow="))) {
			$leftArrow = substr($meta[$i],strrpos($meta[$i],"=")+1);
			if ($leftArrow=="") $leftArrow=0;
		} elseif (is_numeric(strpos($meta[$i],"rightarrow="))) {
			$rightArrow = substr($meta[$i],strrpos($meta[$i],"=")+1);
			if ($rightArrow=="") $rightArrow=0;
		} elseif (is_numeric(strpos($meta[$i],"pagenumbers="))) {
			$pageNumbers = substr($meta[$i],strrpos($meta[$i],"=")+1);
			if ($pageNumbers=="") $pageNumbers=0;
		} elseif (is_numeric(strpos($meta[$i],"thumbnails="))) {
			$thumbnails = substr($meta[$i],strrpos($meta[$i],"=")+1);
			if ($thumbnails=="") $thumbnails=0;
		} elseif (is_numeric(strpos($meta[$i],"preloader="))) {
			$preloader = substr($meta[$i],strrpos($meta[$i],"=")+1);
			if ($preloader=="") $preloader=0;
		} elseif (is_numeric(strpos($meta[$i],"fill="))) {
			$fill = substr($meta[$i],strrpos($meta[$i],"=")+1);
		} elseif (is_numeric(strpos($meta[$i],"bgcolor="))) {
			$bgColor = substr($meta[$i],strrpos($meta[$i],"=")+1);
		} elseif (is_numeric(strpos($meta[$i],"bgmode="))) {
			$bgMode = substr($meta[$i],strrpos($meta[$i],"=")+1);
		} elseif(is_numeric(strpos($meta[$i],"="))) {
			$otherParams .= ", ".$meta[$i];
		}
	}
	// si ces valeurs sont mauvaises, valeurs par défaut
	if (strlen($width)>0) {
		if(!is_numeric($width)) $width = "100%";
	} else {
		$width = get_option('slgallery_standard_width');
	}
	if (strlen($height)>0) {
		if(!is_numeric($height)) $height = "100%";
	} else {
		$height = get_option('slgallery_standard_height');
	}
	if ($leftArrow=="1") $leftArrow="true";
	if ($leftArrow=="0") $leftArrow="false";
	if (!($leftArrow=="true" || $leftArrow=="false")) $leftArrow = get_option('slgallery_standard_leftarrow');
	if ($rightArrow=="1") $rightArrow="true";
	if ($rightArrow=="0") $rightArrow="false";
	if (!($rightArrow=="true" || $rightArrow=="false")) $rightArrow = get_option('slgallery_standard_rightarrow');
	if ($pageNumbers=="1") $pageNumbers="true";
	if ($pageNumbers=="0") $pageNumbers="false";
	if (!($pageNumbers=="true" || $pageNumbers=="false")) $pageNumbers = get_option('slgallery_standard_pagenumbers');
	if ($thumbnails=="1") $thumbnails="true";
	if ($thumbnails=="0") $thumbnails="false";
	if (!($thumbnails=="true" || $thumbnails=="false")) $thumbnails = get_option('slgallery_standard_thumbnails');
	if ($preloader=="1") $preloader="true";
	if ($preloader=="0") $preloader="false";
	if (!($preloader=="true" || $preloader=="false")) $preloader = get_option('slgallery_standard_preloader');
	if ($preloader=="false") {
		$preloader = '<param name="splashscreensource" value="'.urlPlugins("silverlight-gallery").'/ClientBin/preloadSL.xaml" />';
	} else {
		$preloader = '';
	}
	if (!($fill=="uniformtofill" || $fill=="uniform"))  $fill = get_option('slgallery_standard_fill');
	if (strlen($bgColor)<2) {
		$bgColor = get_option('slgallery_standard_bgColor');
	}
	if (!($bgMode=="opaque" || $bgMode=="transparent"))  $bgMode = get_option('slgallery_standard_bgMode');
	if ($bgMode=="opaque") {
		$bgMode = false;
	} else {
		$bgMode = true;
	}
	
  	$minver = get_option('slgallery_standard_version');
	$alternate = stripslashes(get_option('slgallery_alternate_text'));
	$output = str_replace("###WIDTH###", $width, $output);
	$output = str_replace("###HEIGHT###", $height, $output);
	$output = str_replace("###MINVER###", $minver, $output);
	$output = str_replace("###ALTERNATE###", $alternate, $output);
	$output = str_replace("###POSTID###", $post->ID, $output);
	$output = str_replace("###LEFTARROW###", $leftArrow, $output);
	$output = str_replace("###RIGHTARROW###", $rightArrow, $output);
	$output = str_replace("###PAGENUMBERS###", $pageNumbers, $output);
	$output = str_replace("###THUMBNAILS###", $thumbnails, $output);
	$output = str_replace("###PRELOADER###", $preloader, $output);
	$output = str_replace("###FILL###", $fill, $output);
	$output = str_replace("###BGCOLOR###", $bgColor, $output);
	$output = str_replace("###BGMODE###", $bgMode, $output);
	$output = str_replace("###OTHERPARAMS###", $otherParams, $output);
	return ($output);
}

function slgallery_wp_head() {
	echo "<!-- Silverlight WordPress Plugin -->\n<style type=\"text/css\">\n.silverlightControlHost { width:100%; height:100%; }\n</style>";
}

function echo_slgallery($widthP, $heightP) {
	echo slgallery_the_content("[sl-gallery:".$widthP.", ".$heightP."]");
}


add_action('wp_head', 'slgallery_wp_head');
add_filter('the_content', 'slgallery_the_content');

/* ADMIN */

function slgallery_option_page() {
	$standard_width = 'slgallery_standard_width';
	$standard_height = 'slgallery_standard_height';
	$standard_leftarrow = 'slgallery_standard_leftarrow';
	$standard_rightarrow = 'slgallery_standard_rightarrow';
	$standard_pagenumbers = 'slgallery_standard_pagenumbers';
	$standard_thumbnails = 'slgallery_standard_thumbnails';
	$standard_preloader = 'slgallery_standard_preloader';
	$standard_fill = 'slgallery_standard_fill';
	$standard_bgColor = 'slgallery_standard_bgColor';
	$standard_bgMode = 'slgallery_standard_bgMode';
	$minver = 'slgallery_standard_version';
	$alternate = 'slgallery_alternate_text';

	$width_val = get_option($standard_width);
	$height_val = get_option($standard_height);
	$leftarrow_val = get_option($standard_leftarrow);
	$rightarrow_val = get_option($standard_rightarrow);
	$pagenumbers_val = get_option($standard_pagenumbers);
	$thumbnails_val = get_option($standard_thumbnails);
	$preloader_val = get_option($standard_preloader);
	$fill_val = get_option($standard_fill);
	$bgColor_val = get_option($standard_bgColor);
	$bgMode_val = get_option($standard_bgMode);
	$minver_val = get_option($minver);
	$alternate_val = get_option($alternate);


  	if ('insert' == $_POST['action']) {
        update_option($standard_width, $_POST[$standard_width]);
        update_option($standard_height, $_POST[$standard_height]);
		if ($_POST[$standard_leftarrow] == "on")
			update_option($standard_leftarrow, "true");	
		else 
			update_option($standard_leftarrow, "false");
		if ($_POST[$standard_rightarrow] == "on")
			update_option($standard_rightarrow, "true");	
		else 
			update_option($standard_rightarrow, "false");	
		if ($_POST[$standard_pagenumbers] == "on")
			update_option($standard_pagenumbers, "true");	
		else 
			update_option($standard_pagenumbers, "false");
		if ($_POST[$standard_thumbnails] == "on")
			update_option($standard_thumbnails, "true");	
		else 
			update_option($standard_thumbnails, "false");
		if ($_POST[$standard_preloader] == "on")
			update_option($standard_preloader, "true");	
		else 
			update_option($standard_preloader, "false");
        update_option($standard_fill, $_POST[$standard_fill]);
		update_option($standard_bgColor, $_POST[$standard_bgColor]);
		update_option($standard_bgMode, $_POST[$standard_bgMode]);
		update_option($minver, $_POST[$minver]);
		update_option($alternate, $_POST[$alternate]);
		$width_val = get_option($standard_width);
		$height_val = get_option($standard_height);
		$leftarrow_val = get_option($standard_leftarrow);
		$rightarrow_val = get_option($standard_rightarrow);
		$pagenumbers_val = get_option($standard_pagenumbers);
		$thumbnails_val = get_option($standard_thumbnails);
		$preloader_val = get_option($standard_preloader);
		$fill_val = get_option($standard_fill);
		$bgColor_val = get_option($standard_bgColor);
		$bgMode_val = get_option($standard_bgMode);
		$minver_val = get_option($minver);
		$alternate_val = stripslashes(get_option($alternate));
	?>
		<div class="updated"><p><strong><?php _e('Silverlight Gallery default settings updated.', 'mt_trans_domain' ); ?></strong></p></div>
	<?php } ?>
	<!-- Start Options -->
        <div class="wrap">
          <h2>Silverlight Gallery Options</h2>
          <form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
          		<table border="0" cellpadding="0" cellspacing="15">                   
                    <tr>
                    	<td width="200px"><strong><label>Standard Width</label>: </strong></p></td>
                        <td><input name="<?php echo $standard_width; ?>" value="<?php echo $width_val; ?>" type="text" /></td>
                    </tr>
                    
                    <tr>
                    	<td width="200px"><strong><label>Standard Height</label>: </strong></p></td>
                        <td><input name="<?php echo $standard_height; ?>" value="<?php echo $height_val; ?>" type="text" /></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Display left arrow</label>: </strong></p></td>
						<td><input name="<?php echo $standard_leftarrow; ?>" type="checkbox" <?php if ($leftarrow_val=="true") echo 'checked="yes"'; ?>/></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Display right arrow</label>: </strong></p></td>
						<td><input name="<?php echo $standard_rightarrow; ?>" type="checkbox" <?php if ($rightarrow_val=="true") echo 'checked="yes"'; ?>/></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Display page numbers</label>: </strong></p></td>
						<td><input name="<?php echo $standard_pagenumbers; ?>" type="checkbox" <?php if ($pagenumbers_val=="true") echo 'checked="yes"'; ?>/></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Display thumbnails</label>: </strong></p></td>
						<td><input name="<?php echo $standard_thumbnails; ?>" type="checkbox" <?php if ($thumbnails_val=="true") echo 'checked="yes"'; ?>/></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Display preloader</label>: </strong></p></td>
						<td><input name="<?php echo $standard_preloader; ?>" type="checkbox" <?php if ($preloader_val=="true") echo 'checked="yes"'; ?>/></td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Fill mode</label>: </strong></p></td>
						<td>
							<select name="<?php echo $standard_fill; ?>">
								<option value="uniformtofill" <?php if ($fill_val=="uniformtofill") echo 'selected="selected"'; ?>>Uniform to Fill  </option>
								<option value="uniform" <?php if ($fill_val=="uniform") echo 'selected="selected"'; ?>>Uniform  </option>
							</select>
						</td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Background mode</label>: </strong></p></td>
						<td>
							<select name="<?php echo $standard_bgMode; ?>" onchange="javascript:if (document.getElementById('bgMode').value == 'transparent') document.getElementById('bgColor').value = 'transparent';return 0;" id="bgMode">
								<option value="opaque" <?php if ($fill_val=="opaque") echo 'selected="selected"'; ?>>Opaque</option>
								<option value="transparent" <?php if ($fill_val=="transparent") echo 'selected="selected"'; ?>>Transparent</option>
							</select>
						</td>
                    </tr>

					<tr>
                    	<td width="200px"><strong><label>Background color</label>: </strong></p></td>
						 <td><input name="<?php echo $standard_bgColor; ?>" value="<?php echo $bgColor_val; ?>" type="text" style="width:100px" id="bgColor"/></td>
                    </tr>

					<tr>
                    	<td width="200px"><label><strong>Minimum Version :</strong><br /><em> (change at your own risk)</em></label></p></td>
                        <td><input name="<?php echo $minver; ?>" value="<?php echo $minver_val; ?>" type="text" /></td>
                    </tr>

					<tr>
                    	<td width="200px" valign="top"><label><strong>Alternate HTML :</strong></label></p></td>
                        <td><textarea name="<?php echo $alternate; ?>" rows="4" cols="70" /><?php echo $alternate_val; ?></textarea></td>
                    </tr>

                    <tr>
                    	<td colspan="2"><input name="action" value="insert" type="hidden" /></td>
                    </tr>
                </table>
                <p><div class="submit"><input type="submit" name="Update" value="UpdateSilverlight Gallery plugin"  style="font-weight:bold;" /></div></p>
          </form>
        </div>
	<?php 
} 

function slGallery_admin_menu()
{
	add_option("slgallery_standard_width","400");
	add_option("slgallery_standard_height","300");
	add_option("slgallery_standard_leftarrow","true");
	add_option("slgallery_standard_rightarrow","true");
	add_option("slgallery_standard_pagenumbers","false");
	add_option("slgallery_standard_thumbnails","false");
	add_option("slgallery_standard_preloader","true");
	add_option("slgallery_standard_fill","uniformtofill");
	add_option("slgallery_standard_bgColor","#ffffff");
	add_option("slgallery_standard_bgMode","opaque");
  	add_option("slgallery_standard_version","3.0.40723.0");
	add_option("slgallery_alternate_text",'<a href="http://go.microsoft.com/fwlink/?LinkID=149156&amp;v=3.0.40624.0" style="text-decoration: none;"><img src="http://go.microsoft.com/fwlink/?LinkId=108181" alt="Get Microsoft Silverlight" style="border-style: none"/></a>');
	add_options_page('Silverlight Gallery', 'Silverlight Gallery', 9, __FILE__, 'slgallery_option_page'); 
}

add_action('admin_menu', 'slGallery_admin_menu');

// edit post button

add_action('init', 'slGallery_addbuttons');

function slGallery_addbuttons(){
	   if (!current_user_can('edit_posts') && ! current_user_can('edit_pages') )
	     	return;
	   if ( get_user_option('rich_editing') == 'true') {
	     	add_filter("mce_external_plugins", "add_slgallery_tinymce_plugin");
	     	add_filter('mce_buttons', 'register_slgallery_button');
	   }
}

function register_slgallery_button($buttons) {
   array_push($buttons, "slgallery");
   return $buttons;
}

function urlPlugins($type) {
	if (!defined('WP_CONTENT_URL')) {
	      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content' );
	}
	if (defined('WP_PLUGINS_URL'))  {
		return WP_PLUGINS_URL."/". $type;
	} else {
		return WP_CONTENT_URL."/plugins/". $type;
	}
}

function add_slgallery_tinymce_plugin($plugin_array) {
   $path = urlPlugins("silverlight-gallery")."/editor_plugin.js";
   $plugin_array['slgallery'] = $path;
   return $plugin_array;
}


?>
