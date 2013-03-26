<?php
/**
 * Class template a display video directory
 * @since 1.0.0
 */

class IonVideoDirectoryDisplay {
	function show_directory($v_id) {
		global $wpdb;
		
		$table1 = get_option('v_listings');
		$table2 = get_option('v_directory');
?>
		<div class="wrap">
<?php
		$v_id = explode( ',', $v_id );
		foreach($v_id as $id) {
		$result = $wpdb->get_results("SELECT * FROM $table1 a, $table2 b WHERE a.dir_id = b.id AND b.id = '$id'");
		$result = $result[0];
		$value = json_decode($result->value,true);
		
		/**
		 * Calculate the total duration for one directory
		 * @since 1.0.0
		 * @source http://stackoverflow.com/questions/3172332/convert-seconds-to-hourminutesecond#answer-3172368
		 */
		
		foreach($value as $x => $show) {
			$total_duration += floor($show['duration']['hours'] * 3600) + floor($show['duration']['minutes'] * 60) + floor($show['duration']['seconds']);
		}
		
		$hours = floor($total_duration / 3600);
		$minutes = floor(($total_duration / 60) % 60);
		$seconds = $total_duration % 60;
		
		/**
		 * Ends here.
		 */

?>
		
			<div class="postbox">
				<h3 class="directory-name"><span><?php echo $result->directory_name; ?></span><span style="float:right;"><?php echo $hours  . 'hr ' . str_pad($minutes, 2, 0, STR_PAD_LEFT) . 'm ' . str_pad($seconds, 2, 0, STR_PAD_LEFT) . 's'; ?></span></h3>
				<div class="inside">					
					<ul class="video-list">
					<?php 
						if($result->video_count != 0) :
							$i = 0;
							foreach($value as $list) {
						?>
                        	<li>
							<?php
								global $ion_auth_users;
								if( $ion_auth_users->all($list['options']) ) {
							?>
                            	<a class="show_iframe" href="#<?php echo sanitize_title( $result->directory_name); ?>-<?php echo $i; ?>"><?php echo $list['name']; ?></a><span class="video-duration"><?php if ( $list['duration']['hours'] !== '000' ) : echo abs($list['duration']['hours']); ?>hr<?php endif; ?> <?php echo $list['duration']['minutes']; ?>m <?php echo $list['duration']['seconds']; ?>s</span>
                            <?php } else {  ?> 
                            	<a class="login-pop noaccess" href="#no-access"><?php echo $list['name']; ?></a><span class="video-duration"><?php if ( $list['duration']['hours'] !== '000' ) : echo abs($list['duration']['hours']); ?>hr<?php endif; ?> <?php echo $list['duration']['minutes']; ?>m <?php echo $list['duration']['seconds']; ?>s</span>  
                            <?php	
								}								
								$i++;
							?>
                            </li>
						<?php
                       		}
						else : ?>
                        	<li>No videos available.</li>
					<?php endif; ?>
					</ul><!-- .video-list -->
                    <div class="iframe">
                    	<?php 
							if($result->video_count != 0) : $i = 0;
								foreach($value as $list) {
									if( $ion_auth_users->all($list['options']) ) {
						?>
                        	<div id="<?php echo sanitize_title( $result->directory_name); ?>-<?php echo $i; ?>" class="ion-css">
                            	<?php echo stripslashes(html_entity_decode(( $list['value'] ))); ?>
                            </div>
                        <?php 	
									} $i++;
								}
							endif; 
						?>
                    </div><!-- .iframe -->
				</div>
			</div><!-- .postbox -->
		<?php } ?>
		</div><!-- .wrap -->	
<?php
	}

	function container_tabs( $list_header = array(), $content_list = array() ) {
		$i = 0; $j = 0;
?>
	<div class="ion_tabs">
		<ul>
			<?php foreach($list_header as $list) { ?>
				<li><a href="#tabs-<?php echo $i++; ?>"><?php echo $list; ?></a></li>
			<?php } ?>
		</ul>
	<?php foreach($content_list as $container) { ?>
    	<div id="tabs-<?php echo $j++; ?>"><?php echo do_shortcode( $container ); ?><br class="clear" /></div>        	
	<?php } ?>    
    
    </div>
    
<?php
	}
}

//$ion_video_directory_display = &new IonVideoDirectoryDisplay();
?>