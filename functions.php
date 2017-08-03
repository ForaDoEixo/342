<?php

/**

 * @author Divi Space

 * @copyright 2017

 */

if (!defined('ABSPATH')) die();

define("BACKUP_EMAIL","campanha342agora@gmail.com");

function ds_ct_enqueue_parent() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style('extra_fonts','https://fonts.googleapis.com/css?family=Roboto+Mono:100,100i,300,300i,400,400i,500,500i,700,700i|Rubik+Mono+One|Rubik:300,400,400i,500,500i,700,700i,900,900i|Roboto+Condensed:300,400|Roboto:300,400,700,900');
}



function ds_ct_loadjs() {

	wp_enqueue_script( 'ds-theme-script', get_stylesheet_directory_uri() . '/ds-script.js',

		array( 'jquery' )

		);

}

function js_md5_admin_enqueue($hook) {
	wp_enqueue_script( 'js_md5_admin', get_stylesheet_directory_uri() . '/jquery.md5.js' );
}
add_action( 'admin_enqueue_scripts', 'js_md5_admin_enqueue' );

include("include-fonts.php");

add_action( 'wp_enqueue_scripts', 'ds_ct_enqueue_parent' );

add_action( 'wp_enqueue_scripts', 'ds_ct_loadjs' );

add_action( 'after_setup_theme', 'tqd_theme_setup' );

function tqd_theme_setup()
{
	add_image_size( 'facebook', 1200, 630, true);

}

function fb_opengraph()
{
	global $post, $wp;

	$img_standard=get_stylesheet_directory_uri()."/thumb.png";

	if (is_singular('public_agent'))
	{

		if(has_post_thumbnail($post->ID)) {
			$img_src = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'facebook')[0];
		}
		else
		{
			$img_src = $img_standard;
		}

		$voto = get_post_meta(get_the_ID(),mk_get_option("result"),true);
		$genre = wp_get_post_terms( get_the_ID() , 'public_agent_genre');
		$state = wp_get_post_terms( get_the_ID() , 'public_agent_state');
		$party = wp_get_post_terms( get_the_ID() , 'public_agent_party');
		$nome = ucwords(strtolower(get_the_title()));

		if (is_array($genre)) {
			$genre = $genre[0];
			$genre_slug = $genre->slug;
		}

		$genre_pronoun = $genre_slug=='feminino'?'a':'o';

		if ($voto > 0)
		{

			$title = "Deputad".$genre_pronoun." ".$nome." (".strtoupper($party[0]->slug)."/".strtoupper($state[0]->slug).") é a favor do julgamento de Temer por seus crimes";
			$desc = "Faça pressão, divulgue sua posição e saiba como irão votar os outros parlamentares #342agora";
		}

		else if ($voto < 0)
		{
			$title = "Deputad".$genre_pronoun." ".$nome." (".strtoupper($party[0]->slug)."/".strtoupper($state[0]->slug).") é contra o julgamento de Temer!";
			$desc = "Faça pressão, divulgue sua posição e saiba como irão votar os outros parlamentares #342agora";
		}

		else
		{
			$desc = "Faça pressão, divulgue sua posição e saiba como irão votar os outros parlamentares #342agora";
		}


	}
	else
	{
		$img_src = $img_standard;
		$desc = "Confira a posição de cada parlamentar, envie mensagens e participe da campanha. Precisamos de 342 votos! #342agora";
		$title = "Pressione os deputados para que Michel Temer seja julgado!";
	}
	$img_src = $img_standard;

	$type = 'website';
	$current_url = home_url(add_query_arg(array(),$wp->request))."/";

	?>

	<meta property="og:url" content="<?php echo esc_attr($current_url); ?>"/>
	<meta property="og:type" content="<?php echo esc_attr($type); ?>"/>
	<meta property="og:title" content="<?php echo esc_attr($title); ?>"/>
	<meta property="og:image" content="<?php echo esc_attr($img_src); ?>"/>
	<meta property="og:description" content="<?php echo esc_attr($desc); ?>"/>
	<meta property="og:site_name" content="#342agora" />
	<meta property="og:locale" content="pt_BR" />
	<meta property="fb:app_id" content="367988603661256">

	<link rel="shortcut icon" href="http://342agora.org.br/wp-content/themes/342/favicon.ico" />
	<?php
}

add_action('wp_head', 'fb_opengraph', 5);

include('login-editor.php');

function logo_after_header()
{
	?>
	<div class=" et_pb_row et_pb_row_logo">

		<div class="et_pb_column et_pb_column_4_4">

			<div class="et_pb_module et-waypoint et_pb_image et_pb_animation_fade_in et_pb_image_logo et_always_center_on_mobile et-animated">
				<a href="<?php echo site_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/logo.png" alt="#342agora"></a>
			</div>
		</div>
	</div>
	<?php
}

add_action('wp_head', 'logo_after_header');
// Register and load the widget
function wpb_load_widget() {
	register_widget( 'et_makepressure_result' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

// Creating the widget
class et_makepressure_result extends WP_Widget {

	function __construct() {
		parent::__construct(

// Base ID of your widget
			'et_makepressure_result',

// Widget name will appear in UI
			__('BotaPressão: Placar', 'et_makepressure_result'),

// Widget description
			array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'et_makepressure_result' ), )
			);
	}

	public function mk_result_list_agent($id)
	{
		$state = wp_get_post_terms( $id , 'public_agent_state',true);
		$party = wp_get_post_terms( $id , 'public_agent_party',true);
		$img = get_the_post_thumbnail($id, 'post-thumbnail');

		?>
		<a href="<?php echo get_post_permalink($id); ?>"><div class="et_makepressure_result_agents_item"><div class="et_makepressure_result_agents_thumb"><?php echo $img; ?></div><div class="et_makepressure_result_agents_text"><?php echo get_the_title($id)." (".$party[0]->slug."-".$state[0]->slug.")"; ?></div></div></a>
		<?php
	}

// Creating widget front-end

	public function widget( $args, $instance ) {

// before and after widget arguments are defined by themes
		echo $args['before_widget'];

// This is where you run the code and display the output

		$pas = get_posts(array(
			'post_type' => 'public_agent',
			'orderby' => 'rand',
			'numberposts' => -1
			));
		$positive=0;
		$negative=0;
		$neutral=0;
		$cpas_positive=array();
		$cpas_negative=array();
		$cpas_neutral=array();

		$pas_positive=array();
		$pas_negative=array();
		$pas_neutral=array();

		$email_positive="";
		$email_negative="";
		$email_neutral="";

		$email_positive_count=0;
		$email_negative_count=0;
		$email_neutral_count=0;

		foreach ($pas as $pa) {
			$voto = get_post_meta($pa->ID,$instance["campaign"],true);
			$com = wp_get_post_terms($pa->ID, "public_agent_commission");
			$email = get_post_meta($pa->ID, 'public_agent_email', true);
			if ($voto > 0)
			{
				$positive++;
				if (strpos($com[0]->slug,"ccjc") !== false)
				{
					$cpas_positive[]=$pa;
				}
				else
				{
					$pas_positive[]=$pa;
				}
				if ($email_positive_count < 100)
				{
					if (!empty($email_positive))
					{
						$email_positive .= ',';
					}
					$email_positive .= $email;
					$email_positive_count++;
				}
			}
			else if ($voto < 0)
			{
				$negative++;
				if (strpos($com[0]->slug,"ccjc") !== false)
				{
					$cpas_negative[]=$pa;
				}
				else
				{
					$pas_negative[]=$pa;
				}
				if ($email_negative_count < 100)
				{
					if (!empty($email_negative))
					{
						$email_negative .= ',';
					}
					$email_negative .= $email;
					$email_negative_count++;
				}
			}
			else
			{
				$neutral++;
				if (strpos($com[0]->slug,"ccjc") !== false)
				{
					$cpas_neutral[]=$pa;
				}
				else
				{
					$pas_neutral[]=$pa;
				}
				if ($email_neutral_count < 100)
				{
					if (!empty($email_neutral))
					{
						$email_neutral .= ',';
					}
					$email_neutral .= $email;
					$email_neutral_count++;
				}

			}
		}
		?>
		<div class='et_makepressure_result_wrapper'>
			<div class='et_makepressure_result_header'>CONFIRA O PLACAR ATUAL</div>
			<div class='et_makepressure_result_item negative'>
				<div class="border-wrapper">
					<div class="et_makepressure_result_text_wrapper">
						<span class='et_makepressure_result_item_number'><a href="/contra"><?php echo $negative ?></a></span>
						<span class='et_makepressure_result_item_text'>SÃO CONTRA A</span>
						<span class='et_makepressure_result_item_text'>INVESTIGAÇÃO</span>
					</div>
					<div class="links-wrapper"><a href="mailto:<?php echo $email_negative.','.BACKUP_EMAIL.'?subject=' . get_option('makepressure_email_title') . '&body=' . get_option('makepressure_email_body'); ?>" class='et_makepressure_result_button placar'>FAÇA PRESSÃO<br/>MANDE UM RECADO</a><a href="https://mail.google.com/mail?view=cm&tf=0&to=<?php echo $email_negative.','.BACKUP_EMAIL; ?>&su=<?php echo get_option('makepressure_email_title'); ?>&body=<?php echo get_option('makepressure_email_body'); ?>" target=_blank class="et_makepressure_result_button_gmail">Usa Gmail? Clique aqui.</a></div></div>
					<?php if (!$instance["single"]) { ?>
					<div class="et_makepressure_result_agents_wrapper">
						<?php $count=0; foreach ($cpas_negative as $pa) {
							$this->mk_result_list_agent($pa->ID);
							$count++;
							if ($count >= 10)
								break;
						}
						if ($count < 10){
							foreach ($pas_negative as $pa) {
								$this->mk_result_list_agent($pa->ID);
								$count++;
								if ($count >= 10)
									break;
							}
						}
						?>
						<div class="et_makepressure_result_agents_item more"><a href="/contra"><i class="fa fa-angle-double-right" aria-hidden="true"></i><span>VER TODOS</span></a></div>
					</div>
					<?php } ?>
				</div>
				<div class='et_makepressure_result_item neutral'>
					<div class="border-wrapper">
						<div class="et_makepressure_result_text_wrapper">
							<span class='et_makepressure_result_item_number'><a href="/ausente"><?php echo $neutral ?></a></span>
							<span class='et_makepressure_result_item_text'>ESTÃO</span>
							<span class='et_makepressure_result_item_text'>AUSENTES</span>
						</div>
						<div class="links-wrapper"><a href="mailto:<?php echo $email_neutral.','.BACKUP_EMAIL.'?subject=' . get_option('makepressure_email_title') . '&body=' . get_option('makepressure_email_body'); ?>" class='et_makepressure_result_button placar'>FAÇA PRESSÃO<br/>MANDE UM RECADO</a><a href="https://mail.google.com/mail?view=cm&tf=0&to=<?php echo $email_neutral.','.BACKUP_EMAIL; ?>&su=<?php echo get_option('makepressure_email_title'); ?>&body=<?php echo get_option('makepressure_email_body'); ?>" target=_blank class="et_makepressure_result_button_gmail"">Usa Gmail? Clique aqui.</a></div></div>
						<?php if (!$instance["single"]) { ?>
						<div class="et_makepressure_result_agents_wrapper">
							<?php $count=0; foreach ($cpas_neutral as $pa) {
								$this->mk_result_list_agent($pa->ID);
								$count++;
								if ($count >= 10)
									break;
							}
							if ($count < 10){
								foreach ($pas_neutral as $pa) {
									$this->mk_result_list_agent($pa->ID);
									$count++;
									if ($count >= 10)
										break;
								}
							}
							?>
							<div class="et_makepressure_result_agents_item more"><a href="/ausente"><i class="fa fa-angle-double-right" aria-hidden="true"></i><span>VER TODOS</span></a></div>
						</div>
						<?php } ?>
					</div>
					<div class='et_makepressure_result_item positive'>
						<div class="border-wrapper">
							<div class="et_makepressure_result_text_wrapper">
								<span class='et_makepressure_result_item_number'><a href="/a-favor"><?php echo $positive ?></a></span>
								<span class='et_makepressure_result_item_text'>SÃO A FAVOR</span>
								<span class='et_makepressure_result_item_text'>INVESTIGAÇÃO</span>
							</div>
							<div class="links-wrapper"><a href="mailto:<?php echo $email_positive.','.BACKUP_EMAIL.'?subject=' . get_option('makepressure_email_title') . '&body=' . get_option('makepressure_email_body'); ?>" class='et_makepressure_result_button placar'>MOSTRE SEU APOIO<br/>MANDE UM RECADO</a><a href="https://mail.google.com/mail?view=cm&tf=0&to=<?php echo $email_positive.','.BACKUP_EMAIL; ?>&su=<?php echo get_option('makepressure_email_title'); ?>&body=<?php echo get_option('makepressure_email_body'); ?>" target=_blank class="et_makepressure_result_button_gmail">Usa Gmail? Clique aqui.</a></div></div>
							<?php if (!$instance["single"]) { ?>
							<div class="et_makepressure_result_agents_wrapper">
								<?php $count=0; foreach ($cpas_positive as $pa) {
									$this->mk_result_list_agent($pa->ID);
									$count++;
									if ($count >= 10)
										break;
								}
								if ($count < 10){
									foreach ($pas_positive as $pa) {
										$this->mk_result_list_agent($pa->ID);
										$count++;
										if ($count >= 10)
											break;
									}
								}
								?>
								<div class="et_makepressure_result_agents_item more"><a href="/a-favor"><i class="fa fa-angle-double-right" aria-hidden="true"></i><span>VER TODOS</span></a></div>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php
					echo $args['after_widget'];
				}

// Widget Backend
				public function form( $instance ) {

					if ( isset( $instance[ 'campaign' ] ) ) {
						$title = $instance[ 'campaign' ];
					}

// Widget admin form
					?>
					<p>
						<label for="<?php echo $this->get_field_id( 'campaign' ); ?>"><?php _e( 'Campanha:' ); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'campaign' ); ?>" name="<?php echo $this->get_field_name( 'campaign' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
					</p>
					<?php
				}

// Updating widget replacing old instances with new
				public function update( $new_instance, $old_instance ) {
					$instance = array();
					$instance['campaign'] = ( ! empty( $new_instance['campaign'] ) ) ? strip_tags( $new_instance['campaign'] ) : '';
					return $instance;
				}


} // Class et_makepressure_result ends here




function makepressure_all() {
	/*$string .= "
	<script>
	( function( $ ) {
		$( '#makepressure_hidden_emails' ).load( function() {

			$('.et_makepressure_result_button').attr('href','mailto:'+$('#makepressure_hidden_emails').val()+'?subject=". get_option('makepressure_email_title') . "&body=". get_option('makepressure_email_body')."');
			$('.et_makepressure_result_button_gmail').attr('href','https://mail.google.com/mail?view=cm&tf=0&to='+$('#makepressure_hidden_emails').val()+'&su=". get_option('makepressure_email_title') . "&body=". get_option('makepressure_email_body')."');

		} )

} )( jQuery );
</script>";*/
$string .= '<div class="et_make_pressure_button_wrapper regional"><a class="et_makepressure_result_button">FAÇA PRESSÃO<br>MANDE UM RECADO</a><a target=_blank class="et_makepressure_result_button_gmail">Usa Gmail? Clique aqui.</a></div>';
return $string;
}
add_shortcode('make_pressure_current', 'makepressure_all');


function makepressure_filter_func() {
	ob_start();
	?>
	<style>
		.highlight
		{
		color: yellow !important;
	}
	.filter_wrap
	{
	text-align: center;
}
.filter_wrap .dashicons-search {
font-size: 32px;
}
.filter_wrap #agent_filter {
font-size: 22px;
}
</style>
<div class="filter_wrap"><input id="agent_filter" type="text" placeholder="Procure seu deputado"><span class="dashicons dashicons-search"></span></div>
<script>
	jQuery.fn.highlight = function(pat) {
	function innerHighlight(node, pat) {
	var skip = 0;
	if (node.nodeType == 3) {
	var pos = node.data.toUpperCase().indexOf(pat);
	pos -= (node.data.substr(0, pos).toUpperCase().length - node.data.substr(0, pos).length);
	if (pos >= 0) {
	var spannode = document.createElement('span');
	spannode.className = 'highlight';
	var middlebit = node.splitText(pos);
	var endbit = middlebit.splitText(pat.length);
	var middleclone = middlebit.cloneNode(true);
	spannode.appendChild(middleclone);
	middlebit.parentNode.replaceChild(spannode, middlebit);
	skip = 1;
}
}
else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
for (var i = 0; i < node.childNodes.length; ++i) {
i += innerHighlight(node.childNodes[i], pat);
}
}
return skip;
}
return this.length && pat && pat.length ? this.each(function() {
innerHighlight(this, pat.toUpperCase());
}) : this;
};

jQuery.fn.removeHighlight = function() {
return this.find("span.highlight").each(function() {
this.parentNode.firstChild.nodeName;
with (this.parentNode) {
replaceChild(this.firstChild, this);
normalize();
}
}).end();
};


</script>
<script>
	jQuery.extend(jQuery.expr[":"], {
	"containsNC": function(elem, i, match, array) {
	return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});
jQuery("#agent_filter").on("change paste keyup", function()
{
	var val = jQuery(this).val();
	jQuery(".makepressure_grid_item").removeHighlight();
	jQuery(".makepressure_grid_item:not(.empty)").hide();
	jQuery(".makepressure_grid_item:containsNC('"+val+"')").show();
	jQuery(".makepressure_grid_item").highlight(jQuery(this).val());
});
</script>

<?php
$string = ob_get_contents();
ob_end_clean();
return $string;
}
add_shortcode('make_pressure_current_filter', 'makepressure_filter_func');





function makepressure_result_single() {
	ob_start();
	the_widget("et_makepressure_result",array("single"=>true,"campaign"=>mk_get_option("result")));
	$string = ob_get_contents();
	ob_end_clean();
	return $string;
}
add_shortcode('makepressure_result_single', 'makepressure_result_single');

include_once("mk-result.php");

function makepressure_updated_public_agent( $null, $object_id, $meta_key, $meta_value, $prev_value )
{
	if ($meta_value > 0)
	{
		wp_set_post_terms($object_id, get_term_by( 'slug','a_favor','public_agent_vote')->term_id, 'public_agent_vote');
	}
	else if ($meta_value < 0)
	{
		wp_set_post_terms($object_id, get_term_by( 'slug','contra','public_agent_vote')->term_id, 'public_agent_vote');
	}
	else
	{
		wp_set_post_terms($object_id, get_term_by( 'slug','indeciso','public_agent_vote')->term_id, 'public_agent_vote');
	}
}

add_action( "updated_public_agent_meta", 'makepressure_updated_public_agent', 10, 4 );


?>
