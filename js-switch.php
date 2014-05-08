<?php

/*
  Plugin Name: JS-SWITCH
  Plugin URI: http://jsswitch.host56.com/
  Description: Scrollbox that scroll images with that box
  Version: 1.0
  Author: Abhay Jain
  Author URI: http://jsswitch.host56.com/
  License: GPLv2 or later
 */

$mfw_options = get_option('mfw_settings');


function fwds_slider_activation() {

}

register_activation_hook(__FILE__, 'fwds_slider_activation');

function fwds_slider_deactivation() {

}

register_deactivation_hook(__FILE__, 'fwds_slider_deactivation');




add_action('wp_enqueue_scripts', 'fwds_scripts');

function fwds_scripts() {
    global $post;

    wp_enqueue_script('jquery');

    wp_register_script('slidesjs_core', plugins_url('js/jquery.scrollbox.js', __FILE__), array("jquery"));
    wp_enqueue_script('slidesjs_core');

    wp_register_script('slidesjs_init', plugins_url('js/hello.js', __FILE__));
    wp_enqueue_script('slidesjs_init');
}
add_action('wp_enqueue_scripts', 'fwds_styles');

function fwds_styles() {

    wp_register_style('slidesjs_example', plugins_url('css/demo.css', __FILE__));
    wp_enqueue_style('slidesjs_example');
    wp_register_style('slidesjs_fonts', plugins_url('css/font-awesome.min.css', __FILE__));
    wp_enqueue_style('slidesjs_fonts');
}

add_shortcode("JS-SWITCH", "fwds_display_slider");

function fwds_display_slider($attr, $content) {

    extract(shortcode_atts(array(
                'id' => ''
                    ), $attr));

    $gallery_images = get_post_meta($id, "_fwds_gallery_images", true);
    $gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();
    $gallery_images1 = get_post_meta($id, "_fwds_gallery_images1", true);
    $gallery_images1 = ($gallery_images1 != '') ? json_decode($gallery_images1) : array();



    $plugins_url = plugins_url();


    $html = '<div class="container switch">
    <div id="demo5" class="scroll-img">
    <ul class="slides clients" style="transition-duration: 0.6s; transform: translate3d(0px, 0px, 0px);">';
    foreach ($gallery_images as $index => $gal_img) {
            if ($gal_img != "") {
            $html .= "<li style='display: block; float: left;'><a href=''><img src='". $gallery_images[$index] ."'>";
            if($gallery_images1[$index] != ""){
            $html .= "<img class='color-img' src='". $gallery_images1[$index] ."'></a></li>";} 
         }
    }


    $html .= '</ul>
    </div>
  </div>';

    return $html;
}

add_action('init', 'fwds_register_slider');

function fwds_register_slider() {
    $labels = array(
        'menu_name' => _x('JS-SWITCH', 'slidesjs_slider'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Slideshows',
        'supports' => array('title','editor'),
        'menu_icon' => plugins_url( 'js-switch/images/blue_fire.png' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type('slidesjs_slider', $args);
}

/* Define shortcode column in Rhino Slider List View */
add_filter('manage_edit-slidesjs_slider_columns', 'fwds_set_custom_edit_slidesjs_slider_columns');
add_action('manage_slidesjs_slider_posts_custom_column', 'fwds_custom_slidesjs_slider_column', 10, 2);

function fwds_set_custom_edit_slidesjs_slider_columns($columns) {
    return $columns
    + array('slider_shortcode' => __('Shortcode'));
}

function fwds_custom_slidesjs_slider_column($column, $post_id) {

    $slider_meta = get_post_meta($post_id, "_fwds_slider_meta", true);
    $slider_meta = ($slider_meta != '') ? json_decode($slider_meta) : array();

    switch ($column) {
        case 'slider_shortcode':
            echo "[JS-SWITCH id='$post_id' /]";
            break;
    }
}

add_action('add_meta_boxes', 'fwds_slider_meta_box');

function fwds_slider_meta_box() {

    add_meta_box("fwds-slider-images", "Slider Images", 'fwds_view_slider_images_box', "slidesjs_slider", "normal");
}

function fwds_view_slider_images_box() {
    global $post;

    $gallery_images = get_post_meta($post->ID, "_fwds_gallery_images", true);
    // print_r($gallery_images);exit;
    $gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();
     $gallery_images1 = get_post_meta($post->ID, "_fwds_gallery_images1", true);
    // print_r($gallery_images);exit;
    $gallery_images1 = ($gallery_images1 != '') ? json_decode($gallery_images1) : array();

    // Use nonce for verification
    $html = '<input type="hidden" name="fwds_slider_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    $html .= '<p style="color:red"> <b>Note</b> - You can add maximum 10 images and minimum 1 image </p><br /><table class="form-table">';

    $html .= "
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 1</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[0] . "'  /></td>
            <th style=''><label for='Upload Images'> Image 1:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[0] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 2</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[1] . "' /></td>
            <th style=''><label for='Upload Images'> Image 2:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[1] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 3</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text'  value='" . $gallery_images[2] . "' /></td>
            <th style=''><label for='Upload Images'> Image 3:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[2] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 4</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[3] . "' /></td>
            <th style=''><label for='Upload Images'> Image 4:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[3] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 5</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[4] . "' /></td>
            <th style=''><label for='Upload Images'> Image 5:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[4] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 6</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[5] . "'  /></td>
            <th style=''><label for='Upload Images'> Image 6:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[5] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 7</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[6] . "'  /></td>
            <th style=''><label for='Upload Images'> Image 7:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[6] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 8</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[7] . "'  /></td>
            <th style=''><label for='Upload Images'> Image 8:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[7] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 9</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[8] . "'  /></td>
            <th style=''><label for='Upload Images'> Image 9:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[8] . "'  /></td>
          </tr>
          <tr>
            <th style=''><label for='Upload Images'>Path of Image 10</label></th>
            <td><input name='gallery_img[]' id='fwds_slider_upload' type='text' value='" . $gallery_images[9] . "'  /></td>
            <th style=''><label for='Upload Images'>Image 10:Hover</label></th>
            <td><input name='gallery_img1[]' id='fwds_slider_upload' type='text' value='" . $gallery_images1[9] . "'  /></td>
          </tr> 
        </table>";


    echo $html;
}

/* Save Slider Options to database */
add_action('save_post', 'fwds_save_slider_info');

function fwds_save_slider_info($post_id) {


    // verify nonce
    if (!wp_verify_nonce($_POST['fwds_slider_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('slidesjs_slider' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

        /* Save Slider Images */
        //echo "<pre>";print_r($_POST['gallery_img']);exit;
        $gallery_images = (isset($_POST['gallery_img']) ? $_POST['gallery_img'] : '');
        $gallery_images = strip_tags(json_encode($gallery_images));
        update_post_meta($post_id, "_fwds_gallery_images", $gallery_images);
        $gallery_images1 = (isset($_POST['gallery_img1']) ? $_POST['gallery_img1'] : '');
        $gallery_images1 = strip_tags(json_encode($gallery_images1));
        update_post_meta($post_id, "_fwds_gallery_images1", $gallery_images1);

       
    } else {
        return $post_id;
    }
}
?>
