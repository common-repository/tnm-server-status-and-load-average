<?php

	/*
	
	Plugin Name: [TNM] Server Status and Load Average
	Plugin URI: http://www.trevnetmedia.com/
	Description: Wordpress plugin to show the live server load average in the admin bar and on the admin dashboard.
	Version: 1.1
	Author: Christopher Erk
	Author URI: http://www.chriserk.com/
	License: GPL2
	
	*/
	
	if(isset($_GET['la'])) {
		
		$load_average = 'NULL';
		
		$php = phpversion();
		$version = ($php + 0);
		
		if( $version < 5.1 ) {
			die();
		}
			
		$has_exec = true;
		$disabled = explode(',', ini_get('disable_functions'));
		if(in_array('exec', $disabled)) {
			$has_exec = false;
		}
		
		$whoami = 'Unknown';
		if($has_exec == true) $whoami = exec('whoami');
		
		if(function_exists( 'sys_getloadavg' )) {
			$loadresult = sys_getloadavg();
			if($loadresult) $loadresult = $loadresult[0];
		} else {
			
			if($has_exec == true) {
				$loadresult = exec('uptime');
				preg_match("/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $loadresult, $avgs);
				if($avgs) $loadresult = $avgs[0];
			} else {
				$loadresult = 'Unknown';
			}
		}
		
		if($loadresult) $load_average = $loadresult;
		
		echo $load_average;
		die();
	}
	
	function tnm_load_average( $wp_admin_bar ) {
		
		$text = '<span id="tnm-load-average">Load Average: <span style="color: #4EBE00;">x.xx</span></span>';
		
		$options = get_option('tnm_LA_settings');
		
		if(isset($options['tnm_checkbox_field_0'])) {
			$text = '<span id="tnm-load-average"><span style="color: #4EBE00;">x.xx</span></span>';
		}
		
		if(!isset( $options['tnm_checkbox_field_1'] )) {
		
			if ( current_user_can( 'manage_options' ) ) {
				
				$args = array(
					'id'    => 'tnm_load_average',
					'title' => $text,
					'href'  => '#',
					'meta'  => array( 'class' => 'tnm-load-average' )
				);
				$wp_admin_bar->add_node( $args );
				
			}
				
		}
		
	}
	
	add_action( 'admin_bar_menu', 'tnm_load_average', 40 );
	add_action( 'admin_menu', 'tnm_add_admin_menu' );
	add_action( 'admin_init', 'tnm_LA_settings_init' );


	function tnm_add_admin_menu(  ) { 
		add_options_page( 'Server Status', '[TNM] Server Status', 'manage_options', 'tnm_server_status', 'server_status_options_page' );
	}


	function tnm_LA_settings_init(  ) { 

		register_setting( 'pluginPage', 'tnm_LA_settings' );

		add_settings_section(
			'tnm_pluginPage_section', 
			__( 'Options', 'wordpress' ), 
			'tnm_LA_settings_section_callback', 
			'pluginPage'
		);

		add_settings_field( 
			'tnm_checkbox_field_0', 
			__( 'Hide the text "Load Average" in admin bar?', 'wordpress' ), 
			'tnm_checkbox_displayLAtext', 
			'pluginPage', 
			'tnm_pluginPage_section' 
		);

		add_settings_field( 
			'tnm_checkbox_field_1', 
			__( 'Hide the load average in the admin bar completely?', 'wordpress' ), 
			'tnm_checkbox_displayLAadminbar', 
			'pluginPage', 
			'tnm_pluginPage_section' 
		);

		add_settings_field( 
			'tnm_text_field_0', 
			__( 'What is considered a high load on your server?<br /> <em>(will turn red)</em>', 'wordpress' ), 
			'tnm_input_high_load', 
			'pluginPage', 
			'tnm_pluginPage_section' 
		);

		add_settings_field( 
			'tnm_text_field_1', 
			__( 'Load average refresh rate in seconds (admin bar)', 'wordpress' ), 
			'tnm_load_refresh_rate', 
			'pluginPage', 
			'tnm_pluginPage_section' 
		);

	}


	function tnm_checkbox_displayLAtext(  ) { 

		$options = get_option( 'tnm_LA_settings' );
		?>
		<input type='checkbox' name='tnm_LA_settings[tnm_checkbox_field_0]' <?php checked( $options['tnm_checkbox_field_0'], 1 ); ?> value='1'>
		<?php

	}


	function tnm_checkbox_displayLAadminbar(  ) { 

		$options = get_option( 'tnm_LA_settings' );
		?>
		<input type='checkbox' name='tnm_LA_settings[tnm_checkbox_field_1]' <?php checked( $options['tnm_checkbox_field_1'], 1 ); ?> value='1'>
		<?php

	}


	function tnm_input_high_load(  ) { 

		$options = get_option( 'tnm_LA_settings' );
		?>
		<input type='text' name='tnm_LA_settings[tnm_text_field_0]' value='<?php echo $options['tnm_text_field_0']; ?>' placeholder="2">
		<?php

	}


	function tnm_load_refresh_rate(  ) { 

		$options = get_option( 'tnm_LA_settings' );
		?>
		<input type='text' name='tnm_LA_settings[tnm_text_field_1]' value='<?php echo $options['tnm_text_field_1']; ?>' placeholder="60">
		<?php

	}

	function tnm_LA_settings_section_callback(  ) {
		echo __( 'Here you can toggle various options for the plugin.', 'wordpress' );
	}


	function server_status_options_page(  ) { 

		?>
		<form action='options.php' method='post'>
			
			<h2>[TNM] Server Status</h2>
			
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>
			
		</form>
		<?php

	}
	
	
	function tnm_server_stats_widget() {
		wp_add_dashboard_widget('wp_server_load_widget', '<i class="dashicons dashicons-networking sswheading"></i> [TNM] Server Status', 'tnm_server_stats_output');
	}
	add_action('wp_dashboard_setup', 'tnm_server_stats_widget' );


	function tnm_server_stats_output() {
		
		$php = phpversion();
		$version = ($php + 0);
		
		if( $version < 5.1 ) {
			echo '<p style="color: #8F0000">PHP version outdated, must be at least 5.1.3</p>';
		}
			
		$has_exec = true;
		$disabled = explode(',', ini_get('disable_functions'));
		if(in_array('exec', $disabled)) {
			$has_exec = false;
		}
		
		$whoami = 'Unknown';
		if($has_exec == true) $whoami = exec('whoami');
		
		if(function_exists( 'sys_getloadavg' )) {
			$loadresult = sys_getloadavg();
			if($loadresult) $loadresult = implode(', ', $loadresult);
		} else {
			
			if($has_exec == true) {
				$loadresult = exec('uptime');
				preg_match("/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $loadresult, $avgs);
				if($avgs) $loadresult = implode(', ', $avgs);
			} else {
				$loadresult = 'Unknown';
			}
		}
		
		$path	= get_home_path();
		$mysql	= mysql_get_server_info();
		$hostname	= gethostname();
		
		$ip	= $_SERVER['SERVER_ADDR'];
			
		$data = '';
		$data .= '<table>';
		$data .= '<tr><td><b>Host Name</b></td><td>&nbsp;&nbsp;</td><td>: ' . $hostname . '</td></tr>';
		$data .= '<tr><td><b>Server IP</b></td><td>&nbsp;&nbsp;</td><td>: ' . $ip . '</td></tr>';
		$data .= '<tr><td><b>Server Path</b></td><td>&nbsp;&nbsp;</td><td>: ' . $path . '</td></tr>';
		if($loadresult) $data .= '<tr><td><b>Load Averages</b></td><td>&nbsp;&nbsp;</td><td>: ' . $loadresult . '</td></tr>';
		if($uptime) $data .= '<tr><td><b>Server is UP Since</b></td><td>&nbsp;&nbsp;</td><td>: '. $uptime . '</td></tr>';
		$data .= '<tr><td><b>PHP Version</b></td><td>&nbsp;&nbsp;</td><td>: '. $php . '</td></tr>';
		$data .= '<tr><td><b>MySQL Version</b></td><td>&nbsp;&nbsp;</td><td>: '. $mysql . '</td></tr>';
		if($whoami) $data .= '<tr><td><b>PHP user</b></td><td>&nbsp;&nbsp;</td><td>: '. $whoami . '</td></tr>';
		$data .= '</table>';
			
		echo $data;
	}
	
	function custom_admin_js() {
		
		$options = get_option('tnm_LA_settings');
		$hide = (isset($options['tnm_checkbox_field_1'])) ? $options['tnm_checkbox_field_1'] : 0;
		
		$max = (isset($options['tnm_text_field_0'])) ? $options['tnm_text_field_0'] : 2;
		$mid = floor( $max / 2 );
		
		$refresh_rate = (isset($options['tnm_text_field_1'])) ? $options['tnm_text_field_1'] : 60;
		$refresh_rate = $refresh_rate * 1000;
		
		if ( current_user_can( 'manage_options' && $hide != 1 ) ) {
	
			$url = plugins_url() . '/tnm-server-status/tnm-server-status.php?la=true';

		?>

			<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				
				function loadlink(){
					$.get('<?php echo $url; ?>', function ( data ) {

						var load_average = parseFloat( data );
						
						$('#tnm-load-average > span').text( load_average );
						
						if( load_average <= <?php echo $mid; ?> ) {
							$('#tnm-load-average > span').attr('style', 'color: #1FA462');
						} else if( load_average <= <?php echo $max; ?> ) {
							$('#tnm-load-average > span').attr('style', 'color: #fcbf3e');
						} else {
							$('#tnm-load-average > span').attr('style', 'color: #D93C2F');
						}
						 
						 
					});
					
				}

				loadlink();
				
				setInterval(loadlink, <?php echo $refresh_rate; ?>);
				
			});
			</script>
		
		<?php
	
		}

	}
	add_action('admin_footer', 'custom_admin_js');
	add_action('wp_footer', 'custom_admin_js');
	
	// Add settings link on plugin page
	function your_plugin_settings_link($links) { 
	  $settings_link = '<a href="options-general.php?page=tnm_server_status">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	 
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );