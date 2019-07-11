<?php

   // The functionality is to add an Alternate Title meta box to Gutenberg pages backend editor


   /**
    *
    * Adding actual input block to the editor
    *
    */ 
    function alternate_title_gutenberg_add_meta_box() {
      global $current_screen;

      if(method_exists($current_screen, "is_block_editor") && $current_screen->is_block_editor()) {
         add_meta_box(
            "alternate_title_gutenberg_meta_box", //id
            __("Alternate Title", "alternate-title"), // title
            "alternate_title_gutenberg_meta_box_content" // callback
         );
      }
   }
   add_action("add_meta_boxes", "alternate_title_gutenberg_add_meta_box");

   /**
    *
    * Adding the text and placeholder text to the meta box
    *
    * @param   object $post object
    *
    */ 
   function alternate_title_gutenberg_meta_box_content($post) {
      $alternate_title = get_alternate_title($post->ID);
      $title           = __("Enter title here", "alternate-title");
      $placeholder     = $title . "...";
      ?>
      <input type="text" 
            value="<?php echo $alternate_title; ?>"
            id="alternate-title" 
            class="components-text-control__input"
            name="alternate_post_title"
            title="<?php echo $title; ?>"
            placeholder="<?php echo $placeholder; ?>"
      />
      <?php
   }

   /**
    *
    * Registers a meta key
    *
    */ 

   function alternate_title_gutenberg_register_meta() {
      register_meta(
         "any",
         "_alternate_title",
         [
            "type"         => "string",
            "single"       => true,
            "show_in_rest" => true
         ]
      );
   }
   add_action("init", "alternate_title_gutenberg_register_meta");


   /**
    *
    * Updates the alternate title after page is saved/published
    *
    * @param   int a post ID
    * @return  bool/int
    *
    */ 

    function alternate_title_edit_post($post_id) {
      if(!isset($_POST["alternate_post_title"])) {
         return false;
      }

      // Don't autosave 
      if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
         return false;
      }

      // Important to check user permissions before saving
      
      if(isset($_POST["post_type"]) && "page" === $_POST["post_type"]) {
         if(!current_user_can("edit_page", $post_id)) {
            return false;
         }
      }
      else if(!current_user_can("edit_post", $post_id)) {
         return false;
      }

      // Updates the value of an existing meta key (custom field) for the specified p
      return update_post_meta (
         $post_id,
         "_alternate_title",
         stripslashes($_POST["alternate_post_title"])
      );
   }
   add_action("save_post", "alternate_title_edit_post");