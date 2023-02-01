<?php
function install_db() {
  global $wpdb;			
  $easy_gallery_db_version = '1.2';

  $easyGallery = $wpdb->prefix . 'easy_gallery';
  $easyImages = $wpdb->prefix . 'easy_gallery_images';
  
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		  
  if ( !$wpdb->get_var( "SHOW TABLES LIKE '$easyGallery'" ) ) {
			
	$sql = "CREATE TABLE $easyGallery (".
		"Id INT NOT NULL AUTO_INCREMENT, ".
		"name VARCHAR( 60 ) NOT NULL, ".
		"slug VARCHAR( 60 ) NOT NULL, ".
		"description TEXT NOT NULL, ".
		"thumbnail LONGTEXT NOT NULL, ".
		"thumbwidth INT, ".
		"thumbheight INT, ".
		"PRIMARY KEY Id (Id) ".
		")";
	
	dbDelta( $sql );
  }
  
  if ( !$wpdb->get_var( "SHOW TABLES LIKE '$easyImages'" ) ) {
  
	$sql = "CREATE TABLE $easyImages (".
			"Id INT NOT NULL AUTO_INCREMENT, ".
			"gid INT NOT NULL, ".
			"imagePath LONGTEXT NOT NULL, ".
			"title VARCHAR( 50 ) NOT NULL, ".
			"description LONGTEXT NOT NULL, ".
			"sortOrder INT NOT NULL, ".
			"PRIMARY KEY Id (Id) ".
			")";

	dbDelta( $sql );

	add_option( "easy_gallery_db_version", array($this, $easy_gallery_db_version) );
  }
}
?>