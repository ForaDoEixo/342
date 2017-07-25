<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
if ( ! class_exists( 'Makepressure_Result_Options' ) ) {

	class Makepressure_Result_Options {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'Makepressure_Result_Options', 'add_admin_menu' ) );
				add_action( 'admin_init', array( 'Makepressure_Result_Options', 'register_settings' ) );
			}

		}

		/**
		 * Returns all theme options
		 *
		 * @since 1.0.0
		 */
		public static function get_result_options() {
			return get_option( 'result_options' );
		}

		/**
		 * Returns single theme option
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_option( $id ) {
			$options = self::get_result_options();
			if ( isset( $options[$id] ) ) {
				return $options[$id];
			}
		}

		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function add_admin_menu() {
			add_menu_page(
				esc_html__( 'BotaPressão Placar', 'text-domain' ),
				esc_html__( 'BotaPressão Placar', 'text-domain' ),
				'manage_options',
				'mk-placar-settings',
				array( 'Makepressure_Result_Options', 'create_admin_page' )
				);
		}

		/**
		 * Register a setting and its sanitization callback.
		 *
		 * We are only registering 1 setting so we can store all options in a single option as
		 * an array. You could, however, register a new setting for each option
		 *
		 * @since 1.0.0
		 */
		public static function register_settings() {
			register_setting( 'result_options', 'result_options', array( 'Makepressure_Result_Options', 'sanitize' ) );
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $options ) {


				// Input
				if ( ! empty( $options['result'] ) ) {
					$options['result'] = sanitize_text_field( $options['result'] );
				} else {
					unset( $options['result'] ); // Remove from options if empty
				}


			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>

		<div class="wrap">

			<h1><?php esc_html_e( 'BotaPressão Placar', 'text-domain' ); ?></h1>

			<form method="post" action="options.php">

				<?php settings_fields( 'result_options' ); ?>

				<table class="form-table wpex-custom-admin-login-table">



					<?php // Text input example ?>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Campanha', 'text-domain' ); ?></th>
						<td>
							<?php $value = self::get_theme_option( 'result' ); ?>
							<input type="text" name="result_options[result]" value="<?php echo esc_attr( $value ); ?>">
						</td>
					</tr>


				</table>

				<?php submit_button("Atualizar campanha"); ?>

			</form>
			<?php if (mk_get_option("result")) { ?>
			<form id="mk_pa_vote" method="post" >
				<?php submit_button("Atualizar votos"); ?><div id="mk-ajax-print"></div>
				<?php
				$pas = get_posts(array(
					'post_type' => 'public_agent',
					'numberposts' => -1,
					'order'=> 'ASC', 'orderby' => 'title'
					));

				foreach ($pas as $pa) {
					$nome = $pa->post_title;
					$img = get_the_post_thumbnail($pa->ID, 'post-thumbnail');
					$voto = get_post_meta($pa->ID,mk_get_option("result"),true);
					echo "<p class='mk_pa_vote'> ".$img.$nome." Voto: <input type='hidden' name='".$pa->ID."[name]' value='".$pa->post_title."''><input class='mk_pa_votoantigo' type='hidden' name='".$pa->ID."[votoantigo]' value='".$voto."'><input class='mk_pa_voto' type='text' name='".$pa->ID."[voto]' value='".$voto."''></p>";
				}

				?>
			</form>
			<?php } ?>

		</div><!-- .wrap -->
		<?php }

	}
}
new Makepressure_Result_Options();

// Helper function to use in your theme to return a theme option value
function mk_get_option( $id = '' ) {
	return Makepressure_Result_Options::get_theme_option( $id );
}

add_action( 'admin_footer', 'mk_update_pa_votes' ); // Write our JS below here

function mk_update_pa_votes() { ?>
<script type="text/javascript" >
jQuery(document).submit(function(e){
	var form = jQuery(e.target);
	if(form.is("#mk_pa_vote") && (!jQuery("#mk_pa_vote input[type=submit]").hasClass("disabled"))) {
		e.preventDefault();
		jQuery("#mk_pa_vote input[type=submit]").addClass("disabled");
		var sdata = form.serialize();
		var data = {
			'action': 'mk_update_pa_votes_func',
			'data': sdata,
			'hash': jQuery.md5(sdata),
			'var_count': sdata.split("&").length
		};

		jQuery.post(ajaxurl, data, function(response,textStatus) {
			console.log(response);
			jQuery("#mk-ajax-print").html(response);
			jQuery("#mk_pa_vote input[type=submit]").removeClass("disabled");
			jQuery("#mk_pa_vote p.mk_pa_vote").each(function() {

				jQuery(this).children(".mk_pa_votoantigo").val(jQuery(this).children(".mk_pa_voto").val());
			});
		});
	}
});
</script> <?php
}


add_action( 'wp_ajax_my_action', 'mk_update_pa_votes_func' );

function mk_update_pa_votes_func() {
	global $wpdb; // this is how you get access to the database

	$params = array();
	parse_str($_POST['data'], $params);
	if ($_POST['hash'] == md5(http_build_query($params)))
	{
		foreach ($params as $id => $pa) {
			if ($pa["voto"] != $pa["votoantigo"])
			{
				if (update_post_meta($id,mk_get_option("result"),$pa["voto"],$pa["votoantigo"]) == false)
				{
					echo "Erro: ".$pa["name"]." ".mk_get_option("result")." ".$pa["votoantigo"]."->".$pa["voto"]."<br/>";
				}
				else
				{
					echo "Sucesso: ".$pa["name"]." ".mk_get_option("result")." ".$pa["votoantigo"]."->".$pa["voto"]."<br/>";
				}
			}

			if ($pa["voto"] > 0)
			{
				wp_set_post_terms($id, get_term_by( 'slug','a_favor','public_agent_vote')->term_id, 'public_agent_vote');
			}
			else if ($pa["voto"] < 0)
			{
				wp_set_post_terms($id, get_term_by( 'slug','contra','public_agent_vote')->term_id, 'public_agent_vote');
			}
			else
			{
				wp_set_post_terms($id, get_term_by( 'slug','indeciso','public_agent_vote')->term_id, 'public_agent_vote');
			}

		}
	}
	else
	{
		echo "<br>Os votos não foram atualizados porque a integridade dos dados não bate. Para solucionar o problema, aumente a variavel <code>max_input_vars</code> no <code>php.ini</code><br><code>max_input_vars</code>:".ini_get('max_input_vars')." (atual)<br><code>max_input_vars</code>:".$_POST['var_count']." (minimo ideal)";
	}




	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_mk_update_pa_votes_func', 'mk_update_pa_votes_func' );

?>
