<?php 
	/*
	Plugin Name: WEBroad ADS
	Plugin URI: http://webroad.pl/cms/6007-tworzenie-wtyczki-wordpress-3 
	Description: Prosta wtyczka umieszczająca reklamę Google AdSense w treści wpisu
	Version: 1.0
	Author: Michal Kortas
	Author URI: http://webroad.pl
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/
?>
<?php 

class wbrd_ads
{

    private $options;

public function __construct() {
	add_filter( 'the_content', array( $this, 'edit_content' )); 
	add_action('admin_menu', array( $this, 'add_page' ));
	add_action('admin_init', array( $this, 'page_init' ));
}

public function ad_code() {
	$this->options = get_option('ads');
	return
	'<p>
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="'.$this->options['ads_client'].'"
			 data-ad-slot="'.$this->options['ads_slot'].'"
			 data-ad-format="auto"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	</p>';
}

public function edit_content($content) {
	$my_ad = $this->ad_code();
	if(is_single()) {
		return str_replace('<span id="more-1"></span>', '<span id="more-1"></span>'.$my_ad, $content);
	}
	else return $content;
}

public function add_page() {
	add_options_page(
		'Settings Plugin', 
		'WEBroad ADS', 
		'manage_options', 
		'ads_settings_page', 
		array( $this, 'create_page' )
	);
}

public function create_page() {
	$this->options = get_option( 'ads' );
	?>
	<div class="wrap">
		<h2>Ustawienia WEBroad ADS</h2>           
		<form method="post" action="options.php">
		<?php
			settings_fields( 'ads_options' );   
			do_settings_sections( 'ads_settings_page' );
			submit_button(); 
		?>
		</form>
	</div>
	<?php
}

public function page_init() {        
	register_setting(
		'ads_options',
		'ads',
		array($this, 'sanitize')
	);

	add_settings_section(
		'ads_section', 
		'Zarządzanie reklamą AdSense',
		array( $this, 'section_callback' ),
		'ads_settings_page'
	);  

	add_settings_field(
		'ads_client',
		'ID klienta (ad-client)',
		array( $this, 'ads_client_callback' ),
		'ads_settings_page',
		'ads_section'      
	);

	add_settings_field(
		'ads_slot',
		'ID reklamy (ad-slot)',
		array( $this, 'ads_slot_callback' ),
		'ads_settings_page',
		'ads_section'      
	);	

}

public function sanitize( $input ) {
	$new_input = array();
	if( isset( $input['ads_slot'] ) )
		$new_input['ads_slot'] = sanitize_text_field( $input['ads_slot'] );
	if( isset( $input['ads_client'] ) )
		$new_input['ads_client'] = sanitize_text_field( $input['ads_client'] );
	
	return $new_input;
}

public function section_callback() {
	echo '<p>Skonfiguruj swoją reklamę. Potrzebne informacje znajdziej w kodzie reklamy, wygenerowanym w Google AdSense.</p>';
	echo '<img src="http://ss.webroad.pl/webroad_20160226201530.png" alt="Pomoc">';
}

public function ads_slot_callback() {
	if(isset( $this->options['ads_slot'] )) $ads_slot = esc_attr( $this->options['ads_slot']);
	echo '<input type="text" id="ads_slot" name="ads[ads_slot]" value="'.$ads_slot.'"  placeholder="0000000000">';
}

public function ads_client_callback() {
	if(isset( $this->options['ads_client'] )) $ads_client = esc_attr( $this->options['ads_client']);
	echo '<input type="text" id="ads_client" name="ads[ads_client]" value="'.$ads_client.'" placeholder="ca-pub-111111111111111">';
}
}

$ads_settings_page = new wbrd_ads();
?>