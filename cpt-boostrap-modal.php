<?php
/*
Plugin Name: CPT Bootstrap modal
Plugin URI: 
Description: 
Version: 1.0
Author: Sten Winroth
Author URI: www.innodrift.com
License: GPLv2
*/

// Custom Post Type Setup
add_action( 'init', 'cptModal_post_type' );
function cptModal_post_type() {
	$labels = array(
		'name' => 'Modals',
		'singular_name' => 'Modal',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New modal',
		'edit_item' => 'Edit modal',
		'new_item' => 'New modal',
		'view_item' => 'View modal',
		'search_items' => 'Search modals',
		'not_found' =>  'No modal',
		'not_found_in_trash' => 'No modal found in Trash', 
		'parent_item_colon' => '',
		'menu_name' => 'FAQ'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'page',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 21,
		'supports' => array('title','editor', 'page-attributes', 'custom-fields')
	); 
	register_post_type('cptModal', $args);
}

// FRONT END

// Shortcode
function cptModal_shortcode($atts, $content = null) {
	// Set default shortcode attributes
	$defaults = array(
		'backdrop' => 'true',
		'keyboard' => 'true',
		'show' => 'true'
	);

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($defaults, $atts);

	return cptModal_frontend($atts);
}
add_shortcode('FAQ_modal', 'cptmodal_shortcode');




// Display latest WftC
function cptModal_frontend($atts){
	$args = array( 'post_type' => 'cptModal', 'orderby' => 'menu_order', 'order' => 'ASC');
	$loop = new WP_Query( $args );
	$modals = array();
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( '' != get_the_title() ) {
			$title = get_the_title();
			$content = get_the_content();
			$id = get_the_ID();
			$script = get_post_custom_values("script");
			$modals[] = array('title' => $title, 'content' => $content, 'id' => $id, 'divSpecial' => $divSpecial);
		}
	}
	if(count($modals) > 0){
		ob_start();
		?>
		<!--Table start-->
	
		<table class="table table-hover table-bordered">
		<thead>
		      <tr>
		        <th>#</th>
		        <th>Fråga:</th>
		      </tr>
		</thead>
		<tbody>
			<?php $i = 0; ?>
			<?php foreach ($modals as $key => $title) { ?>
			<?php $i++; ?>
		    <tr>
		      <td><?php echo $i; ?></td>
		      <td><a id="<?php echo $title['id']; ?>" href="#cptmodal_<?php echo $title['id']; ?>" data-toggle="modal"><?php echo $title['title'];?></a></td>
		    </tr>
		    <?php } ?>
		</tbody>
		</table>	
<!--Table end-->
<!--modal start-->
<?php foreach ($modals as $key => $title) { ?>
<div id="cptmodal_<?php echo $title['id']; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo $title['title'];?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo $title['content'] ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-custom" data-dismiss="modal" aria-hidden="true">Stäng</button>
  </div>
</div>
<?php } ?>
<!--Modal end-->
		<?php }
	$output = ob_get_contents();
	ob_end_clean();
	
	// Restore original Post Data
	wp_reset_postdata();	
	
	return $output;
}
?>