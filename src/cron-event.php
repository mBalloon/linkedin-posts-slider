<?php
// Exit if accessed directly.  
if ( !defined('ABSPATH') ) {
  exit;
}

function linkedin_posts_sync_cron() {
  if ( !wp_next_scheduled( 'linkedin_posts_sync_event' ) ) {
    wp_schedule_event( time(), 'daily', 'linkedin_posts_sync_event' );  
  }
}

add_action( 'wp', 'linkedin_posts_sync_cron' );

function linkedin_posts_schedule_cron() {
  if ( !wp_next_scheduled( 'linkedin_posts_sync_event' ) ) {
    wp_schedule_event( time(), 'daily', 'linkedin_posts_sync_event' );
  }
}

register_activation_hook( __DIR__  . '/linkedin-posts-slider.php', 'linkedin_posts_schedule_cron' );


