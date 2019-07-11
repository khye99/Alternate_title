<?php

   // This file contains functions that can be called related to alternate titles plugin


   /**
    *
    * Resets all settings back to the default values
    *
    * @return  bool
    *
    */ 
   function alternate_title_reset_settings() {
      $default_settings = alternate_title_get_default_settings();

      foreach($default_settings as $setting => $default_value) {
         update_option($setting, $default_value);
      }

      return true;
   }

   /**
    *
    * Returns all settings and their default values used by Alternate Title
    * @return  array
    *
    */
   function alternate_title_get_default_settings() {
      // Define default settings and values 
      $default_settings = array(
         "alternate_title_post_ids"               => array(),
         "alternate_title_post_types"             => array(),
         "alternate_title_categories"             => array()
      );

      $default_settings = apply_filters("alternate_title_get_default_settings", $default_settings);

      return (array)$default_settings;
   }

   /**
    * 
    * Returns the alternate title from $post_id
    *
    * @param int    $post_id of the post wanted
    *
    * @return string 
    *
    */
   function get_alternate_title($post_id) {
      // If $post_id not set, use current post ID
      if(!$post_id) {
         $post_id = (int)get_the_ID();
      }

      // Get the alternate title and return false if it's empty actually empty
      $alternate_title = get_post_meta($post_id, "_alternate_title", true);

      if(!$alternate_title) {
         return "";
      }

      $alternate_title = apply_filters("get_alternate_title", $alternate_title, $post_id);
      return (string)$alternate_title;
   }


   /**
    * 
    * Returns whether specific page has alternate title
    *
    * @param int    $post_id of post to check
    *
    * @return bool
    *
    */
   function has_alternate_title($post_id) {
      $alternate_title = get_alternate_title($post_id);
      $hasTitle             = false; //default

      if($alternate_title) {
         $hasTitle = true;
      }
      return $hasTitle;
   }

   /**
    * 
    * Returns all content of pages that DO NOT have alternate titles
    *
    * @param array    $additional query
    *
    * @return array
    *
    */
   function get_posts_without_alternate_title(array $additional_query = array()) {
      $query_arguments = array(
         "post_type"    => "any",
         "meta_key"     => "_alternate_title",
         "meta_value"   => " ",
         "meta_compare" => "==",
         "post_status"  => "publish"
      );

      $query_arguments = wp_parse_args($query_arguments, $additional_query);

      return get_posts($query_arguments);
   }

   /**
    * 
    * Returns all content of pages that DO have alternate titles
    *
    * @param array    $additional query
    *
    * @return array
    *
    */
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

   /**
    * 
    * Returns only the alternate titles if they exist
    *
    * @param array    $additional query
    *
    * @return array
    *
    */
   function get_all_alternate_titles(array $additional_query = array()) {
      $arrayContents = get_posts_with_alternate_title($additional_query);
      $titleArray = [];
      $size = count($arrayContents);   // get the count of how many posts there are here
		for ($i = 0; $i < $size; $i++) {
				array_push($titleArray, get_alternate_title($arrayContents[$i]->ID)); 
      }
      
      return $titleArray;
   }

   /**
    * 
    * Returns only the original titles if alternate titles DON'T exist
    *
    * @param array    $additional query
    *
    * @return array
    *
    */
   function get_titles_without_alternate_titles(array $additional_query = array()) {
      $arrayContents = get_posts_without_alternate_title($additional_query);
      $titleArray = [];
      $size = count($arrayContents);  
		for ($i = 0; $i < $size; $i++) {
				array_push($titleArray, get_the_title($arrayContents[$i]->ID)); 
      }

      return $titleArray;
   }

   /**
    * 
    * Returns all the titles of pages, alternate and original titles
    *
    * @param array    $additional query
    *
    * @return array
    *
    */
   function get_all_correct_titles() {
      $titles = wp_parse_args(get_all_alternate_titles(), get_titles_without_alternate_titles());
      sort($titles);
      return $titles;
   }