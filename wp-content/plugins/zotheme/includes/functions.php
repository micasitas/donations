<?php

global $zo_html_id;

if (empty($zo_html_id)) {
    $zo_html_id = array();
}
/**
 * Require libraries if needed.
 *
 * @access public
 *
 */
function zoResizeLib(){
	//check if lib exists
	if(!function_exists('mr_image_resize')){
		require_once(ZO_LIBRARIES.'mr-image-resize.php');
	}
	return;
}
function zoGetCategoriesByPostID($post_ID = null,$taxo = 'category'){
    $term_cats = array();
    $categories = get_the_terms($post_ID,$taxo);
    if($categories){
        foreach($categories as $category){
            $term_cats[] = get_term( $category, $taxo );
        }
    }
    return $term_cats;
}
/**
 * Generator unique html id
 * @param type $id: string
 */
function zoHtmlID($id) {
    global $zo_html_id;
    $id = str_replace(array('_'), '-', $id);
    if (isset($zo_html_id[$id])) {
        $count = count($zo_html_id[$id]);
        $zo_html_id[$id][$count] = 1;
        $count++;
        return $id . '-' . $count;
    } else {
        $zo_html_id[$id] = array(1);
        return $id;
    }
}

function zoFileScanDirectory($dir, $mask, $options = array(), $depth = 0) {
    $options += array(
        'nomask' => '/(\.\.?|CSV)$/',
        'callback' => 0,
        'recurse' => TRUE,
        'key' => 'uri',
        'min_depth' => 0,
    );

    $options['key'] = in_array($options['key'], array('uri', 'filename', 'name')) ? $options['key'] : 'uri';
    $files = array();
    if (is_dir($dir) && $handle = opendir($dir)) {
        while (FALSE !== ($filename = readdir($handle))) {
        	if (!preg_match($options['nomask'], $filename) && $filename[0] != '.') {
                $uri = "$dir/$filename";
                if (is_dir($uri) && $options['recurse']) {
                    // Give priority to files in this folder by merging them in after any subdirectory files.
                    $files = array_merge(zoFileScanDirectory($uri, $mask, $options, $depth + 1), $files);
                } elseif ($depth >= $options['min_depth'] && preg_match($mask, $filename)) {
                    // Always use this match over anything already set in $files with the
                    // same $$options['key'].
                    $file = new stdClass();
                    $file->uri = $uri;
                    $file->filename = $filename;
                    $file->name = pathinfo($filename, PATHINFO_FILENAME);
                    $files[$filename] = $file;
                }
            }
        }
        closedir($handle);
    }
    return $files;
}
?>