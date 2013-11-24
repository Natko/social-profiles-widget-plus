<?php 
/*
Plugin Name: Social Profiles Widget Plus
Plugin URI: http://natko.com
Description: Display links to your social profiles.
Author: Natko Hasić
Author URI: http://natko.com
Version: 1.0
*/

class SocialProfilesWidgetPlus{

	function __construct() {

		// Load plugin text domain
		load_plugin_textdomain( 'social-profiles-widget-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Create the widget
		add_action( 'widgets_init', create_function('', 'register_widget("SocialProfilesWidgetPlus_Widget");'));

		// Add scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'spwp_social_widget_scripts' ), 999 );
	}

	public function spwp_social_widget_scripts($hook){
		if('widgets.php' == $hook){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );

			wp_register_script( 'spwp-social-widget', plugins_url( '/script/widget-social.js', __FILE__ ) ); 
			wp_enqueue_script( 'spwp-social-widget' );

			wp_localize_script( 'spwp-social-widget', 'socialwidget', array( 
				'profilelink' => __('Profile link...', 'social-profiles-widget-plus'),
				'emailaddress' => __('Email address or contact page link...', 'social-profiles-widget-plus'),
				'skypemessage' => __('Skype username...', 'social-profiles-widget-plus'),
				)
			);
		}
	}
}

new SocialProfilesWidgetPlus();

///////////////////////////////////////////////////////////////////////////////////////
// Widget
///////////////////////////////////////////////////////////////////////////////////////

class SocialProfilesWidgetPlus_Widget extends WP_Widget {

	public $icons_list;

	function __construct() {

		///////////////////////////////////////////////////////////////////////////////////////
		// Social Profiles
		///////////////////////////////////////////////////////////////////////////////////////

		$this->icons_list = array(
			'addthis' => 'AddThis',
			'apple' => 'Apple',
			'behance' => 'Behance',
			'blogger' => 'Blogger',
			'deviantart' => 'DeviantArt',
			'digg' => 'Digg',
			'dribbble' => 'Dribbble',
			'email' => 'Email / Contact',
			'facebook' => 'Facebook',
			'feedburner' => 'Feedburner',
			'flickr' => 'Flickr',
			'forrst' => 'Forrst',
			'github' => 'GitHub',
			'googleplus' => 'Google+',
			'grooveshark' => 'Grooveshark',
			'instagram' => 'Instagram',
			'lastfm' => 'LastFM',
			'linkedin' => 'LinkedIn',
			'myspace' => 'Myspace',
			'newsvine' => 'Newsvine',
			'pinterest' => 'Pinterest',
			'rss' => 'RSS',
			'sharethis' => 'ShareThis',
			'skype' => 'Skype',
			'soundcloud' => 'SoundCloud',
			'squidoo' => 'Squidoo',
			'tumblr' => 'Tumblr',
			'twitter' => 'Twitter',
			'vimeo' => 'Vimeo',
			'vk' => 'VK',
			'windows' => 'Windows',
			'wordpress' => 'WordPress',
			'youtube' => 'YouTube',
			'zerply' => 'Zerply',
		);

		parent::WP_Widget( 'spwp_social_widget', 'Social Profiles', array( 'classname' => 'widget-social-icons', 'description' => __('All of your social profiles in one place.', 'social-profiles-widget-plus') ), array( 'width' => 300, 'height' => 350) );
	
		if ( is_active_widget(false, false, $this->id_base) ){
			add_action( 'wp_head', array( $this, 'spwp_social_widget_style') );
		}

	}

	public function spwp_social_widget_style(){
		echo "\n\n\t\t<!-- Social Profiles Widget Plus - http://natko.com/plugins/social-profiles-widget-plus -->
		<style type=\"text/css\">
			.spwp-social-icons-wrapper { overflow: hidden; margin: 0 0 -10px 0; }
			.spwp-social-icons-wrapper a{ float: left; margin: 0 10px 10px 0; }
		</style>
		<!-- / Social Profiles Widget Plus -->\n\n";
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$template_dir = get_template_directory_uri();

		$instance_social_id = '';
		$instance_social_url = '';
		$title = '';
		$description = '';
		$version = 'regular';

		if(isset($instance['social-id'])){ $instance_social_id = $instance['social-id']; }
		if(isset($instance['social-url'])){ $instance_social_url = $instance['social-url']; }
		if(isset($instance['title'])){ $title = strip_tags($instance['title']); }
		if(isset($instance['description'])){ $description = $instance['description']; }
		if(isset($instance['version'])){ $version = $instance['version']; }

		if($version == 'flat'){ 
			$version_path = 'flat/'; 
		} else { 
			$version_path = 'regular/';
		}

		echo $before_widget;

		//////////////////////////////////////////////////
		//  Display widget title
		//////////////////////////////////////////////////

		if($title != ''){
			echo $before_title . $instance['title'] . $after_title;
		}

		//////////////////////////////////////////////////
		//  Display widget description
		//////////////////////////////////////////////////

		if($description != ''){
			echo '<p>'. $instance['description'] .'</p>';
		}

		//////////////////////////////////////////////////
		//  Display social icons
		//////////////////////////////////////////////////

		if(isset($instance['social-id'])){ $i = 0;

			echo '<div class="spwp-social-icons-wrapper">';

			foreach ( $instance_social_id as $id){

				////////////////////////////////////////////////////////////////////////////////////////////////////
				// If the current icon is not empty/email/skype then echo normal <a href>
				// Else if the current icon is email and contains an email address echo <a mailto>
				// Else if the current icon is skype echo <a href="skype:">
				////////////////////////////////////////////////////////////////////////////////////////////////////

				if($id != 'empty' && $id != 'email' && $id != 'skype' || ($id == 'email' && strpos($instance_social_url[$i], '@') === false )){ ?>

					<a href="<?php echo $instance_social_url[$i]; ?>" class="<?php echo $id; ?>"><img src="<?php echo plugins_url( 'images/' , __FILE__ ); echo $version_path; echo $id; ?>.png" alt="<?php echo $id; ?>"></a>

				<?php } else if ($id == 'email' && strpos($instance_social_url[$i],'@') != false) { ?>

					<a href="mailto:<?php echo $instance_social_url[$i]; ?>" class="<?php echo $id; ?>"><img src="<?php echo plugins_url( 'images/' , __FILE__ ); echo $version_path; echo $id; ?>.png" alt="<?php echo $id; ?>"></a>

				<?php } else if ($id == 'skype') { ?>

					<a href="skype:<?php echo $instance_social_url[$i]; ?>" class="<?php echo $id; ?>"><img src="<?php echo plugins_url( 'images/' , __FILE__ ); echo $version_path; echo $id; ?>.png" alt="<?php echo $id; ?>"></a>

				<?php }
				
				$i++; 

			}

			echo '</div>';

		}

		echo $after_widget;

	}
	
	//////////////////////////////////////////////////
	//  U P D A T E   W I D G E T
	//////////////////////////////////////////////////
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['social-id'] = $new_instance['social-id'];
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = $new_instance['description'];
		$instance['version'] = $new_instance['version'];

		$social_url_check = $new_instance['social-url'];

		$i = 0;

		foreach ( $social_url_check as $url){
			$new_url = str_replace(' ', '', $url);

			if($instance['social-id'][$i] != 'email' && $instance['social-id'][$i] != 'skype'){

				if($new_url != ''){
					$social_url_prefix = substr( $new_url, 0, 4 );
					if($social_url_prefix != 'http'){
						$new_url = 'http://' . $new_url;
					}

					$social_url_check[$i] = $new_url;
				}

			} else if($instance['social-id'][$i] == 'email') {
				if (strpos($social_url_check[$i],'@') !== false) {
					$social_url_check[$i] = $new_url;
				} else {
					if($new_url != ''){
						$social_url_prefix = substr( $new_url, 0, 4 );
						if($social_url_prefix != 'http'){
							$new_url = 'http://' . $new_url;
						}

						$social_url_check[$i] = $new_url;
					}
				}

			} else if($instance['social-id'][$i] == 'skype') {
				$social_url_check[$i] = $new_url;
			}

			$i++;
		}

		$instance['social-url'] = $social_url_check;

		return $instance;
	}

	//////////////////////////////////////////////////
	//   W I D G E T   S E T T I N G S
	//////////////////////////////////////////////////
	
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance );

		$instance_social_id = '';
		$instance_social_url = '';
		$instance_social_title = '';
		$instance_social_description = '';
		$instance_social_version = 'regular';

		if(isset($instance['social-id'])){
			$instance_social_id = $instance['social-id'];
		}

		if(isset($instance['social-url'])){
			$instance_social_url = $instance['social-url'];
		}

		if(isset($instance['title'])){
			$instance_social_title = strip_tags($instance['title']);
		}

		if(isset($instance['description'])){
			$instance_social_description = $instance['description'];
		}

		if(isset($instance['version'])){
			$instance_social_version = $instance['version'];
		}

		?>

		<style type="text/css">

			.spwp-social-sortable{
				padding-top: 15px;
				margin-top: 10px;
				border-top: 1px solid #E7E7E7;
			}

			.spwp-social-single{
				border-bottom: 1px solid #E7E7E7;
				padding-bottom: 15px;
				margin-bottom: 15px;
				overflow: hidden;
			}

			.spwp-social-single.dummy{
				display: none;
			}

			.spwp-social-icon-preview{
				width: 32px;
				height: 32px;
				border-radius: 3px;
				display: block;
				background-color: #EFEFEF;
				float: left;
				cursor: move;
			}

			.spwp-social-icon-preview.empty{
				width: 30px;
				height: 30px;
				border: 1px dashed #D9D9D9;
			}

			<?php
				$themedir = get_template_directory_uri();
				$plugin_url = plugins_url( '/script/widget-social.js', __FILE__ );
				foreach ($this->icons_list as $profile_id => $profile_value) {
					echo '.spwp-social-icon-preview.'.$profile_id.'{ background-image: url("'.plugins_url( 'images/' , __FILE__ ).'regular/'.$profile_id.'.png"); }' . "\n";
					echo '.spwp-social-sortable.flat .spwp-social-icon-preview.'.$profile_id.'{ background-image: url("'.plugins_url( 'images/' , __FILE__ ).'flat/'.$profile_id.'.png"); }' . "\n";
				}
			?>

			.spwp-social-single select.spwp-profile-name{
				width: 85%;
				margin-bottom: 7px;
				float: right;
			}

			.spwp-social-single input.spwp-profile-link{
				width: 85%;
				float: right;
			}

			.spwp-social-new{
				float: right;
				margin-bottom: 10px;
			}

			.spwp-social-remove{
				color: #C1C1C1;
				float: left;
				font-weight: 900;
				margin-left: 14px;
				margin-right: 14px;
				margin-top: 5px;
				text-decoration: none;
				outline: none;
			}

			.spwp-social-remove:focus{
				outline: none;
			}

			.social-widget-placeholder{
				background-color: #FFF;
				border-radius: 3px;
				padding-bottom: 15px;
				margin-bottom: 14px;
				border: 1px dashed #D9D9D9;
			}

			.spwp-social-single.ui-sortable-helper{
				border-bottom: none;
			}

			.spwp-social-clear{
				clear: both;
			}

			label.spwp-social-title{
				padding-top: 10px;
			}

			label.spwp-social-description{
				padding-top: 10px;
				float: left;
			}

			label.spwp-social-description + input{
				margin-bottom: 10px;
				
			}

		</style>

		<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="spwp-social-title"><?php echo __('Title:', 'social-profiles-widget-plus') ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo strip_tags($instance_social_title); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" style="width:100%;">

		<label for="<?php echo $this->get_field_id( 'description' ); ?>" class="spwp-social-description"><?php echo __('Description:', 'social-profiles-widget-plus') ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'description' ); ?>" value="<?php echo htmlentities($instance_social_description); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" class="widefat" style="width:100%;">

		<label for="<?php echo $this->get_field_id( 'version' ); ?>" class="spwp-social-version"><?php echo __('Version:', 'social-profiles-widget-plus') ?></label>
		<select autocomplete="off" id="<?php echo $this->get_field_id( 'version' ); ?>" class="widefat social-change-style" name="<?php echo $this->get_field_name( 'version' ); ?>">
			<option value="regular" <?php if($instance_social_version == 'regular'){ echo 'selected="selected"'; } ?>><?php echo __('Regular Icons', 'social-profiles-widget-plus'); ?></option>
			<option value="flat" <?php if($instance_social_version == 'flat'){ echo 'selected="selected"'; } ?>><?php echo __('Flat Icons', 'social-profiles-widget-plus'); ?></option>
		</select>

		<div class="spwp-social-single dummy">
			<span class="spwp-social-icon-preview empty"></span>
			<select autocomplete="off" data-id="<?php echo $this->get_field_id( 'social-id' ); ?>" class="spwp-profile-name" data-name="<?php echo $this->get_field_name( 'social-id' ); ?>[]">
				<option value="empty"><?php echo __('&lt;select icon&gt;', 'social-profiles-widget-plus'); ?></option>
				<?php foreach ($this->icons_list as $profile_id => $profile_value) {
					echo '<option value="'.$profile_id.'">'.$profile_value.'</option>'."\n";
				} ?>
			</select>
			<input data-id="<?php echo $this->get_field_id( 'social-url' ); ?>" class="spwp-profile-link" type="text" placeholder="<?php echo __('Profile link...', 'social-profiles-widget-plus'); ?>" data-name="<?php echo $this->get_field_name( 'social-url' ); ?>[]">
			<a href="" class="spwp-social-remove">x</a>
		</div>

		<div class="spwp-social-sortable <?php if($instance_social_version == 'flat'){ echo 'flat'; } ?>">

			<?php if(isset($instance['social-id'])){ $i = 0; ?>

				<?php foreach ( $instance_social_id as $id){ ?>

					<div class="spwp-social-single">
						<span class="spwp-social-icon-preview <?php echo $id; ?>"></span>
						<select autocomplete="off" id="<?php echo $this->get_field_id( 'social-id' ); ?>" class="spwp-profile-name" name="<?php echo $this->get_field_name( 'social-id' ); ?>[]">
							<option <?php if ( $id == 'none' ) echo 'selected="selected"'; ?> value="empty"><?php echo __('&lt;select icon&gt;', 'social-profiles-widget-plus'); ?></option>
							<?php foreach ($this->icons_list as $profile_id => $profile_value) {
								echo '<option value="'.$profile_id.'"';
								if ( $id == $profile_id ){
									echo 'selected="selected"';
								}
								echo '>'.$profile_value.'</option>'."\n";
							} ?>
						</select>
						<?php if ($id != 'skype' && $id != 'email'){ ?>
							<input id="<?php echo $this->get_field_id( 'social-url' ); ?>" class="spwp-profile-link" type="text" placeholder="<?php echo __('Profile link...', 'social-profiles-widget-plus'); ?>" value="<?php echo $instance_social_url[$i]; ?>" name="<?php echo $this->get_field_name( 'social-url' ); ?>[]">
						<?php } else if ($id == 'email') { ?>
							<input id="<?php echo $this->get_field_id( 'social-url' ); ?>" class="spwp-profile-link" type="text" placeholder="<?php echo __('Email address or contact page link...', 'social-profiles-widget-plus'); ?>" value="<?php echo $instance_social_url[$i]; ?>" name="<?php echo $this->get_field_name( 'social-url' ); ?>[]">
						<?php } else if ($id == 'skype') { ?>
							<input id="<?php echo $this->get_field_id( 'social-url' ); ?>" class="spwp-profile-link" type="text" placeholder="<?php echo __('Skype username...', 'social-profiles-widget-plus'); ?>" value="<?php echo $instance_social_url[$i]; ?>" name="<?php echo $this->get_field_name( 'social-url' ); ?>[]">
						<?php } ?>
						<a href="" class="spwp-social-remove">x</a>
					</div>

				<?php  $i++; } ?>

			<?php } else { ?>

				<div class="spwp-social-single">
					<span class="spwp-social-icon-preview empty"></span>
					<select autocomplete="off" id="<?php echo $this->get_field_id( 'social-id' ); ?>" class="spwp-profile-name" name="<?php echo $this->get_field_name( 'social-id' ); ?>[]">
						<option value="empty"><?php echo __('&lt;select icon&gt;', 'social-profiles-widget-plus'); ?></option>
						<?php foreach ($this->icons_list as $profile_id => $profile_value) {
							echo '<option value="'.$profile_id.'">'.$profile_value.'</option>'."\n";
						} ?>
					</select>
					<input id="<?php echo $this->get_field_id( 'social-url' ); ?>" class="spwp-profile-link" type="text" placeholder="<?php echo __('Profile link...', 'social-profiles-widget-plus'); ?>" value="<?php echo $instance_social_url; ?>" name="<?php echo $this->get_field_name( 'social-url' ); ?>[]">
					<a href="" class="spwp-social-remove">x</a>
				</div>

			<?php } ?>

		</div>

		<a href="" class="spwp-social-new">+ <?php echo __('Add another', 'social-profiles-widget-plus'); ?></a>
		<div class="spwp-social-clear"></div>

<?php

	}

}

?>