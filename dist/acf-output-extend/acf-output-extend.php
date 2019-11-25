<?php
/*
Plugin Name: Advanced Custom Fields Output Extends
Plugin URI:
Description: Automate the Display Side Output of Advance Custom Fields.
Version: 0.0.1
Author: Kohei Shimizu (Raphael-D)
Author URI: https://github.com/Raphael-D/wordpress/tree/master/acf-output-extend
License: GPL2
*/
/*  Copyright 2019 Kohei Shimizu (Raphael-D) (email : admin@codehack.dev)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! class_exists( 'acf_exporter' ) ) {
  register_activation_hook( __FILE__, array( 'acf_exporter', 'activate' ) );
  register_deactivation_hook( __FILE__, array( 'acf_exporter', 'deactivate' ) );
  class acf_exporter {
    protected function get_post_slug() {
      global $post;
      $state = $post->post_type;
      return $state !== 'post' ? $state : get_the_category()[0]->category_nicename;
    }
    protected function has_check_c_fields() {
      global $post;
      foreach (get_post_meta($post->ID) as $custom_key => $custom_fields) {
        if(get_post_meta($post->ID, $custom_key, true) && get_field_object($custom_key)['label']) {
          $results[$custom_key] = true;
        }
      }
      return array_search(true, $results) ? true : false;
    }
    protected function create_custom_fields() {
      ob_start();
      global $post;
      echo '<table class="wp-table wp-table-bordered custom-field-table custom-field-table--' . $this->get_post_slug() . '">';
      foreach (get_post_meta($post->ID) as $custom_key => $custom_fields) {
        if(get_post_meta($post->ID, $custom_key, true) && get_field_object($custom_key)['label']) {
          echo '<tr class="custom-field-table__row">';
          echo '<th class="custom-field-table__th">' . get_field_object($custom_key)['label'] . '</th>';
          echo '<td class="custom-field-table__td">';
          if(gettype(get_field($custom_key)) === "array") {
            foreach(get_field($custom_key) as $arrays) {
              if($arrays !== end(get_field($custom_key))) {
                echo $arrays;
                echo ",&nbsp;";
              } else {
                echo $arrays;
              }
            }
          } else {
            echo get_field($custom_key);
          }
          echo '</td>';
          echo '</tr>';
        }
      }
      echo '</table>';
      return ob_get_clean();
    }
    protected function get_custom_fields($content) {
      global $post;
      if(is_single() && $this->has_check_c_fields()) {
        $content_custom_fields = '';
        $content_custom_fields = $this->create_custom_fields();
        $content .= $content_custom_fields;
      }
      return $content;
    }
  }// End acf_exporter
  final class my_acf_exporter extends acf_exporter {
    public function get_my_custom_fields($content) {
      return $this->get_custom_fields($content);
    }
  }
  function get_custom_fields($content) {
    $get_custom_fields = new my_acf_exporter();
    return $get_custom_fields->get_my_custom_fields($content);
  }
  add_filter('the_content', 'get_custom_fields', 10);
}// End if
