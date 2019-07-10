<?php

   // This file contains functions that can be called related to alternate titles plugin

   // Resets all settings back to the default values.
   // @return bool

   function alternate_title_reset_settings() {
      $default_settings = alternate_title_get_default_settings();

      foreach($default_settings as $setting => $default_value) {
         update_option($setting, $default_value);
      }
      return true;
   }


    // Returns all settings and their default values used by alternate Title.
    // @return array

   function alternate_title_get_default_settings() {
      // Define default settings and values 
      $default_settings = array(
         "alternate_title_post_types"             => array(),
         "alternate_title_categories"             => array(),
         "alternate_title_post_ids"               => array(),
         "alternate_title_auto_show"              => "on",
         "alternate_title_title_format"           => "%alternate_title%: %title%",
         "alternate_title_input_field_position"   => "above",
         "alternate_title_only_show_in_main_post" => "off",
         "alternate_title_column_position"        => "right"
      );

      $default_settings = apply_filters("alternate_title_get_default_settings", $default_settings);

      return (array)$default_settings;
   }

   // Returns the IDs of the posts for which alternate title is activated.
   // @return array of page/post ID's
 
   function get_alternate_title_post_ids() {
      return (array)alternate_title_get_setting("post_ids");
   }

   /**
    * Get the alternate title from post ID $post_id
    *
    * @param int    $post_id      ID of target post.
    * @param string $prefix       To be added in front of the alternate title.
    * @param string $suffix       To be added after the alternate title.
    * @param bool   $use_settings Use filters set on alternate Title settings page.
    *
    * @return string The alternate title
    *
    *
    */
   function get_alternate_title($post_id = 0, $prefix = "", $suffix = "", $use_settings = false) {
      /** If $post_id not set, use current post ID */
      if(!$post_id) {
         $post_id = (int)get_the_ID();
      }

      /** Get the alternate title and return false if it's empty actually empty */
      $alternate_title = get_post_meta($post_id, "_alternate_title", true);

      if(!$alternate_title) {
         return "";
      }

      /** Use filters set on alternate Title settings page */
      if($use_settings && !alternate_title_validate($post_id)) {
         return "";
      }

      $alternate_title = $prefix . $alternate_title . $suffix;

      /** Apply filters to alternate title if used with Word Filter Plus plugin */
      if(class_exists("WordFilter")) {
         /** @noinspection PhpUndefinedClassInspection */
         $word_filter = new WordFilter;
         /** @noinspection PhpUndefinedMethodInspection */
         $alternate_title = $word_filter->filter_title($alternate_title);
      }

      $alternate_title = apply_filters("get_alternate_title", $alternate_title, $post_id, $prefix, $suffix);

      return (string)$alternate_title;
   }


   // Returns whether specific page/post has alternate title
   // $return bool

   function has_alternate_title($post_id = 0) {
      $alternate_title = get_alternate_title($post_id);
      $hasTitle             = false; //default

      if($alternate_title) {
         $hasTitle = true;
      }
      return $hasTitle;
   }

    // Returns all pages/posts that DO have alternate titles
    // @return array

   function get_posts_with_alternate_title(array $additional_query = array()) {
      $query_arguments = array(
         "post_type"    => "any",
         "meta_key"     => "_alternate_title",
         "meta_value"   => " ",
         "meta_compare" => "!=",
         "post_status"  => "publish"
      );

      $query_arguments = wp_parse_args($query_arguments, $additional_query);

      return get_posts($query_arguments);
   }
