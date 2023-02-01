<?php
/* Plugin Name: nomor_kontak_footer
 * Plugin URI: http://dodytea.com
 * Description: Description
 * Version: 1.0
 * Author: dodytea
 * Author URI: http://dodytea.com
 * Text Domain: text-domain
 * Domain Path: domain-path
 * Network: network
 */
add_action('wp_footer', 'nomor_kontak_footer');
add_action('wp_head', 'nomor_kontak_footer_css');

function nomor_kontak_footer_css(){
echo '<style>'.
		'#kontak-kontainer{'.
		'position:relative;'.

		'}'.

		'#kontak-bar{'.
		'position:fixed;'.
		'bottom:0;'.
		'z-index:999;'.
		'width:100%;'.
		'background-color: #4fbd06;'.
		'border-top:2px solid grey;'.
		'padding:2px;'.
		'text-align:center;'.
		
		'font-style:Arial;'.
		'}'.

		'#kontak-bar img{'.
		'height: 35px;'.
    	'width: 35px;'.
		'vertical-align:middle'.
	 	'}'.

	 	'#kontak-bar a:hover{'.
	 	'color:black;'.
		'font-size:18px'.
		'text-decoration: none;'.
	 	'}'.

	 	'#kontak-bar a{'.
	 	'color:white;'.
		'font-size:18px'.
	 	'}'.


	 '</style>';

}

function nomor_kontak_footer(){
$options = get_option('nomor-kontak-option-fields');
$nomor_telepon = $options['nomor-kontak-nomortlp'];	


echo '<div id="kontak-kontainer">'.
	 	'<div id="kontak-bar"><a href="https://api.whatsapp.com/send?phone='. $nomor_telepon . '" > Chat Via Whatsapp Klik Ini <img src="' . plugin_dir_url( __FILE__ ) . '/WA.png"> </a></div>'.
	 '</div>';


}

// OPTION PAGE 

function nomor_kontak_menu(){
	$page_title 		= 'Nomor Kontak';
	$menu_title 		= 'Nomor Kontak';
	$capability 		= 'manage_options';
	$slug 				= 'nomor-kontak';
	$callable_function 	= 'nomor_kontak_options';

	add_menu_page($page_title, $menu_title ,$capability, $slug , $callable_function);
}

add_action('admin_menu', 'nomor_kontak_menu');

// Halaman  Options Page (Callable Function)
function nomor_kontak_options(){
echo "<H2>Nomor Kontak</H2>"; 


// Save Option
if($_POST['nomor-kontak-submit']){
$options['nomor-kontak-nomortlp'] = $_POST['nomor-kontak-nomortlp'];
update_option('nomor-kontak-option-fields', $options);
echo '<div class="updated"><p> <strong> Options Saved </strong> </p></div>';
}
$options = get_option('nomor-kontak-option-fields')



?>
<!-- FORM -->

<form method="post">
<table class="form-table">
	<tr>
		<td width="100px">
			<label for="nomor-kontak-nomortlp">Nomor Telp:</label>
		</td>
		<td>
			<input 
			type="text" 
			name="nomor-kontak-nomortlp" 
			size="20" 
			id="nomor-kontak-nomortlp" 
			value="<?php echo $options['nomor-kontak-nomortlp'];   ?>">
		</td>
	</tr>
		<td></td>
		<td><input type="submit" name="nomor-kontak-submit" id="nomor-kontak-submit" class="button" value="Simpan"></td>
	<tr>
		
	</tr>
</table>
</form>

<?php }
