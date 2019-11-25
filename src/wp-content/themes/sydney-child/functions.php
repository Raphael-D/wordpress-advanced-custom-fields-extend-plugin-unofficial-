<?php
/**
 * Sydney-child functions and definitions
 *
 */
add_action( 'wp_enqueue_scripts', 'sydney_child_enqueue' );
function sydney_child_enqueue() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
// class acf_exporter {
//   protected function get_post_slug() {
//     global $post;
//     $state = $post->post_type;
//     return $state !== 'post' ? $state : get_the_category()[0]->category_nicename;
//   }
//   protected function has_check_c_fields() {
//     global $post;
//     foreach (get_post_meta($post->ID) as $custom_key => $custom_fields) {
//       if(get_post_meta($post->ID, $custom_key, true) && get_field_object($custom_key)['label']) {
//         $results[$custom_key] = true;
//       }
//     }
//     return array_search(true, $results) ? true : false;
//   }
//   protected function create_custom_fields() {
//     ob_start();
//     global $post;
//     echo '<table class="wp-table wp-table-bordered custom-field-table custom-field-table--' . $this->get_post_slug() . '">';
//     foreach (get_post_meta($post->ID) as $custom_key => $custom_fields) {
//       if(get_post_meta($post->ID, $custom_key, true) && get_field_object($custom_key)['label']) {
//         echo '<tr class="custom-field-table__row">';
//         echo '<th class="custom-field-table__th">' . get_field_object($custom_key)['label'] . '</th>';
//         echo '<td class="custom-field-table__td">';
//         if(gettype(get_field($custom_key)) === "array") {
//           foreach(get_field($custom_key) as $arrays) {
//             if($arrays !== end(get_field($custom_key))) {
//               echo $arrays;
//               echo ",&nbsp;";
//             } else {
//               echo $arrays;
//             }
//           }
//         } else {
//           echo get_field($custom_key);
//         }
//         echo '</td>';
//         echo '</tr>';
//       }
//     }
//     echo '</table>';
//     return ob_get_clean();
//   }
//   protected function get_custom_fields($content) {
//     global $post;
//     if(is_single() && $this->has_check_c_fields()) {
//       $content_custom_fields = '';
//       $content_custom_fields = $this->create_custom_fields();
//       $content .= $content_custom_fields;
//     }
//     return $content;
//   }
// }
// class my_acf_exporter extends acf_exporter {
//   public function get_my_custom_fields($content) {
//     return $this->get_custom_fields($content);
//   }
// }
// function get_custom_fields($content) {
//   $get_custom_fields = new my_acf_exporter();
//   return $get_custom_fields->get_my_custom_fields($content);
// }
// add_filter('the_content', 'get_custom_fields', 10);
