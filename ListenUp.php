<?php
/*
 * Plugin Name: ListenUp
 * Description: Create shortcodes for speech synthesis
 * Author: Larry Aronson
 * Version: 0.9.7
 * Updated: 2022-03-14
 * Author URI: https://LarryAronson.com/
 */

/* main ListenUp shortcode driver function */
function lup_ListenUp ( $atts, $content = null, $tag = '' ) { 
	extract(shortcode_atts(array(  
	'id'    => '',					// Default = current post id 
	'name'	=> '',					// for ListenUpCustom
	'class' => '',					// CSS class for styling 
	'intro' => '', 					// Additional text to be said 
	'voice' => '',					// Voice name 
	'pitch' => '',					// Voical pitch 
	'speed' => '',					// Reading speed 
	'lang'  => '', 					// Voice Language 
	'link'  => 'Hear the excerpt',	// Link text 
	'title' => 'Click to Listen. Click again to Cancel.' 
	), $atts, $tag)); 

/* process content */     

	global $post;									// access current post object
	if (empty($id)) $id = $post->ID;				// This or some other post?  
	$saytext = '';

/* Get content based on the calling shortcode name (tag) */

	if ($tag == 'ListenUpExcerpt') { 				// tag specific process
		$saytext = strip_tags(get_the_excerpt($id));// get the WordPress excerpt 
		if (empty($content)) $content = $link;		// what appears on page  
	}
	elseif ($tag == 'ListenUpCustom') {				// get a single custom field
		if (!empty($name)) {
			$saytext = strip_tags(get_post_meta($id, $name, true));
			if (empty($content)) $content = $name;	// provide a link 
		}
	}
	elseif ($tag == 'ListenUpImage') {				// get the description for an image
		if (!empty($content)) {
			$img_class = strstr($content, 'wp-image-');	// attachment id from class attribute
			$img_class = preg_replace('/[ "].*/', '', $img_class);
			$attID = substr($img_class, 9);
			if (is_numeric($attID)) {
				$attachment = get_post((int) $attID);   // get the attachment object
				$saytext = $attachment->post_content; 	// and here's the image description	
				if (empty($saytext)) $saytext = $attachment->post_excerpt;
			}
		}
	}
	else $saytext = strip_tags($content);			// remove html from content to speak 

/* add an introduction with a short pause */

	if (!empty($intro)) {							
		$saytext = $intro . " , . , . , . " . $saytext; 
	} 

/* clean up the text to be spoken */	
	$saytext = preg_replace('/[[:cntrl:]]/', ' ', $saytext); // replace control chars with blanks 
	$saytext = preg_replace('/[“"”]/', '', $saytext); 		 // lose double quotes
	$saytext = strip_shortcodes($saytext);			// avoid recursion and other problems
	
	if (empty($saytext)) return $content;			// What! Nothing to say? 
	 
	$saytext = addslashes($saytext);				// escape quote marks 

/* process voice attributes */ 

	$parms = array();								// for voice synthesis API 
	
	if (!empty($voice)) { 							// voice can be male, female or some name 
		if (strtolower($voice) == 'male') { 
			$parms[] = "'voice':'Alex'";			// my guy 
			if (empty($lang)) $lang = 'en';			// force lang choice for Chrome & Firefox 
		} 
		elseif (strtolower($voice) == 'female') { 
			$parms[] = "'voice':'Karen'";	    	// I think she's Irish 
			if (empty($lang)) $lang = 'en';			// force voice 
		}  
		else $parms[] = "'voice':'$voice'";
	}

	if (!empty($lang)) $parms[] = "'lang':'$lang'";	// mind your Language 

	if (!empty($speed)) {							// and watch your speed				 
		if (strtolower($speed) == 'fast') $speed = 1.67; 
		elseif (strtolower($speed) == 'slow') $speed = 0.67; 
		else $speed = floatval($speed); 
		$parms[] = "'speed':$speed"; 				// set the speaking rate  
	}
	 
	if (!empty($pitch)) {							// set the vocal pitch 
		if (strtolower($pitch) == 'high') $pitch = 1.75; 
		elseif (strtolower($pitch) == 'low') $pitch = 0.25; 
		else $pitch = floatval($pitch); 
		$parms[] = "'pitch':$pitch"; 
	}
	 
	$sayhow .= '{ ' . join($parms,',') . ' }';		// inline JS Array of voice attributes 
	$sayhow = preg_replace('/[“”]/', '', $sayhow);	// remove double quotes quotes 


/* HTML attributes */    

	if (!empty($class)) {							// set CSS class  
		if (strtolower($class) == 'button') $class = " class=\"ListenUp-button\"";
		else {
			if (strtolower($class) != 'null') $class = " class=\"$class\"";
		}
	}   	
	else {											// no class so set by tagname
		if 		($tag == 'ListenUpExcerpt') $class = " class=\"ListenUp-excerpt\"";
		elseif	($tag == 'ListenUpCustom') 	$class = " class=\"ListenUp-custom\"";
		elseif 	($tag == 'ListenUpImage')	$class = " class=\"ListenUp-image\"";
		else								$class = " class=\"ListenUp-content\"";
	}
	
/* title and language attributes */ 

	if (!empty($title)) $title = 'title="' . addslashes($title) . '"';  
	if (!empty($lang )) $lang  = ' lang="' . preg_replace('/[“”]/', '', $lang) . '"'; 

/* put it all together */

	$onclick = " onclick=\"lup_sayText('$saytext', $sayhow);\""; 
	$attributes = $title . $lang . $class . $onclick;    // attributes for the html tag 

	return "<span $attributes>$content</span>"; 
} 

/* Add a stylesheet with the default CSS */
function lup_ListenUp_styles () { 						// default stylesheet  
	echo '<link rel=stylesheet href="' . plugins_url('ListenUp') . '/ListenUp.css'. '"/>'; 
} 

if (is_admin()) {      						// add readme link to plugins page entry
	include plugin_dir_path( __FILE__ ) . 'ListenUp-admin.php';
}
else {
	add_shortcode('ListenUp', 'lup_ListenUp'); 			// add the 4 shortcode functions 
	add_shortcode('ListenUpExcerpt', 'lup_ListenUp'); 
	add_shortcode('ListenUpCustom', 'lup_ListenUp'); 
	add_shortcode('ListenUpImage', 'lup_ListenUp'); 
	add_action('wp_head', 'lup_ListenUp_styles');
	wp_enqueue_script( 'sayText', plugins_url('ListenUp') . '/sayText.js', array(), null, true ); 
}
?>