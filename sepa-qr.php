<?php
/**
 * @package Payment 
 * @version 1.0
 */
/* 
Plugin Name: Single Euro Payments Area QR Generator
Plugin URI: https://nerghum.com/my-portfolio/payment-qr-generator/
Description: This is a payment qr code generator for The Single Euro Payments Area (SEPA). The Single Euro Payments Area (SEPA) is a payment-integration initiative of the European Union for simplification of bank transfers denominated in euro
Author: Nerghum
Version: 1.0
Author URI: http://nerghum.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
/**
 * This files should always remain compatible with the minimum version of
 * PHP supported by WordPress.
 */

// bloack direct access
if ( ! defined('ABSPATH' ) ) exit;

// define stylesheet and js
function sepa_qr_style_css(){
    wp_register_style( 'style.css', plugin_dir_url( __FILE__ ) . 'style.css', array());
    wp_enqueue_style( 'style.css');

    wp_register_script('qrgenerator', plugin_dir_url( __FILE__ ) . 'qrgenerator.js');
    wp_enqueue_script( 'qrgenerator' );
}

add_action('wp_enqueue_scripts','sepa_qr_style_css');

// add admin setting menu
function sepa_qr_admin_menu() {

add_menu_page('QR Bank Info setting','SEPA Bank Info','manage_options','sepa_bank_info_setting','sepa_qr_bank_info_setting','dashicons-admin-home','99');

}

add_action('admin_menu','sepa_qr_admin_menu');

//add setting link in plugin page

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sepa_qr_setting_link');
function sepa_qr_setting_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'admin.php?page=sepa_bank_info_setting' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

// Setting page content 

function sepa_qr_bank_info_setting(){ ?>
	<h1>SEPA Payment QR Setting Panel</h1>

	<form action="options.php" method="POST">
		<?php 
		do_settings_sections('sepa_bank_info_setting');
		settings_fields('sepa_bank_info_options');
		submit_button(); ?>
	</form>

<?php }

// define widget class

function sepa_qr_setting_options(){

	add_settings_section('sepa_bank_info_options','Fill Up the Info','sepa_qr_option_info','sepa_bank_info_setting');

	add_settings_field('company_name','Company Name','sepa_qr_company_name_input','sepa_bank_info_setting','sepa_bank_info_options');
	register_setting('sepa_bank_info_options','company_name');

	add_settings_field('company_iban','Company IBAN','sepa_qr_company_iban_input','sepa_bank_info_setting','sepa_bank_info_options');
	register_setting('sepa_bank_info_options','company_iban');

	add_settings_field('company_bic','BIC','sepa_qr_company_bic_input','sepa_bank_info_setting','sepa_bank_info_options');
	register_setting('sepa_bank_info_options','company_bic');

	add_settings_field('company_qr_size','QR Image Size(ex: 300)','sepa_qr_company_img_size','sepa_bank_info_setting','sepa_bank_info_options');
	register_setting('sepa_bank_info_options','company_qr_size');

}

add_action('admin_init','sepa_qr_setting_options');

// Setting Option input function

function sepa_qr_option_info() {
	echo 'Please enter all valid data. <br> Use shortcode [pay] for preview the section <br>
	If you need any other customization you can contact me. <a href="http://www.nerghum.com/contact" target="_blank">click hare for contact </a>';
} 

function sepa_qr_company_name_input() {
	echo '<input type="text" name="company_name" value="'.get_option('company_name').'"/>';
}

function sepa_qr_company_iban_input() {
	echo '<input type="text" name="company_iban" value="'.get_option('company_iban').'"/>';
}

function sepa_qr_company_bic_input() {
	echo '<input type="text" name="company_bic" value="'.get_option('company_bic').'"/>';
}

function sepa_qr_company_img_size() {
	echo '<input type="text" name="company_qr_size" value="'.get_option('company_qr_size').'"/>';
}



// ShortCode Generate for UI

function sepa_qr_generator_ui() {

	ob_start(); ?>
	<main class="sepa_qr">
  <section class="qr-section">	
  	<div ID="qrious-section"><img id="qrious" style=""></div>	
    <form autocomplete="off">
      <label>
        <p>Amount</p>
        <input type="text" name="amount" value="" spellcheck="false" id="sepa_input_amount">
      </label>
      <label>
        <p>Reference</p>
        <input type="text" name="reason" value="" spellcheck="false" id="sepa_input_ref">
      </label>
    </form>
    <br>
    <button class="btn-primary qr-btn" onclick="qrshow()">Generate</button>
  </section>
</main>

	<script>

		  (function() {
		    
		    var companyName = "<?php echo get_option('company_name'); ?>";
		    var companyIban = "<?php echo get_option('company_iban'); ?>";
		    var companyBic = "<?php echo get_option('company_bic'); ?>";
		    var companyQrSize = "<?php echo get_option('company_qr_size'); ?>";

		    var $amount = document.querySelector('main form [name="amount"]');
		    	var $reason = document.querySelector('main form [name="reason"]');

		    var qr = window.qr = new QRious({
		      element: document.getElementById('qrious'),
		      size: companyQrSize,
		      value: ''
		    });

		    $amount.value = "";
		    addEventListener('input', function() {
		      qr.value = "bank://singlepaymentsepa?name="+companyName+"&reason="+$reason.value+"&iban="+companyIban+"&bic="+companyBic+"&amount="+$amount.value;

		    });


		  })();
		   
		   function qrshow() {
			  var x = document.getElementById('qrious-section');
			  if (x.style.display === 'none') {
			    x.style.display = 'block';
			  } else {
			    x.style.display = 'block';
			  }
}
	 
	 document.getElementById("sepa_input_amount").style.width = "<?php echo get_option('company_qr_size'); ?>px";
	 document.getElementById("sepa_input_ref").style.width = "<?php echo get_option('company_qr_size'); ?>px";

	</script>
	
	<?php
	    return ob_get_clean();

}
add_shortcode( 'pay', 'sepa_qr_generator_ui' );