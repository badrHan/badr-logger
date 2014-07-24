<?php
/* Plugin name: 바다 로거
   Plugin URI: http://badr.kr
   Author: badr Han
   Author URI: http://badr.kr
   Version: 0.5
   Description: ChromePhp 콘솔을 이용한 디버깅 툴
   Max WP Version: 3.9

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if( !class_exists("ChromePhp") )
		require_once(trailingslashit(dirname(__FILE__)) . "ChromePhp.php");

function logger_footer_end() {
	global $wpdb;
	if( isset($_GET['log']) ) {
		$sql = "SELECT date,log FROM {$wpdb->prefix}badr_log order by id desc limit ".$_GET['log'];
	} else {
	$sql = "SELECT date,log FROM {$wpdb->prefix}badr_log where date > ".(time() - 30)."	order by id desc";
	}
	$fivesdrafts = $wpdb->get_results( $sql );
	echo '<script type="text/javascript">';
	foreach ( $fivesdrafts as $fivesdraft ) {
		$date = date('H:i:s',$fivesdraft->date + (9*60*60));
		$aLog = unserialize($fivesdraft->log);
		$backtrace = $aLog[1];
		$log = base64_encode( utf8_encode(json_encode($aLog[0][0])));
			?>
		if(typeof window.console != 'undefined')
		console.log( '<?php echo '['.$date.'] '.$backtrace?>','\n', JSON.parse(Base64.decode('<?php echo $log?>')) );
	<?php
	}
	echo "</script>";
}

add_action("admin_init", "badr_log_init");


function badr_log_init(){
		add_action( 'admin_footer', 'logger_footer_end', 9999 );
	 	wp_register_script( 'badr-log-script', plugins_url( 'base64.js', __FILE__ ));
		wp_enqueue_script( 'badr-log-script');
}


