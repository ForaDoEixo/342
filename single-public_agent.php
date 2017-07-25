<?php

get_header();

$show_default_title = get_post_meta( get_the_ID(), '_et_pb_show_title', true );

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<style>
#sidebar{display: none;}
#left-area{width: 100%;}
#main-content .container:before {
	width: 0;
}
#main-content .container {
	padding-top: 1em !important;
}
.public_agent_image, .public_agent_info
{
	display: inline-block;
	vertical-align: top;
	font-family: "Rubik", sans-serif;
	font-size: 1em;
	color: #1A1A1A !important;
}
.makepressure_label {
	display: none;
}
.public_agent_info h1 {
	font-family: "Rubik", sans-serif;
	font-weight: bold;
	font-size: 3em;
}
.public_agent_info span {
	font-size: 1.5em;
}
.public_agent_info img {
	width: 2em;
	vertical-align: middle;
	margin-right: 0.5em;
	margin-bottom: 0.5em;
}
.public_agent_info .phone {
	font-weight: bold;
}
.public_agent_info a {
	color: #F15A24 !important;
	word-break: break-all;
}
.public_agent_wrapper {
	display: inline-block;
	margin-left: 5em;
	padding-top: 1em;
	position: relative;
	max-width: 57% !important;
}
.public_agent_image {
	position: absolute;
	left: -5em;
	top: 0;
}
@media (max-width: 479px)
{
	.makepressure_grid_item {
		padding: 1em 0;
	}
	.public_agent_info span:nth-child(-n+3), .public_agent_info h1 {
		display: none;
	}
	.makepressure_label {
		display: inherit;
	}
	.public_agent_wrapper {
		max-width: 90% !important;
		width: 90% !important;
		margin: 0 auto !important;
	}
	.makepressure_post_main_image {
		width: 90% !important;
	}
	.makepressure_label {
		width: 90% !important;
	}
	.makepressure_action {
		width: 90% !important;
	}
}
@media (max-width: 768px)
{
	.makepressure_grid_item {
		width: 100% !important;
		font-size: 4vw;
	}
	.public_agent_info {
		margin-bottom: 2em;
		font-size: 3vw;
		text-align: left;
	}
	.public_agent_image {
		display: none;
	}
	.public_agent_wrapper
	{
		text-align: center;
		max-width: 100% !important;
	}

	.public_agent_info span {
		font-size: 1.5em;
	}
	.container {
		width: 100% !important;
	}
}
@media (min-width: 769px) and (max-width: 980px)
{
	.public_agent_image, .public_agent_info {
		font-size: 2vw;
	}

}
</style>
<?php while ( have_posts() ) : the_post(); ?>
	<?php

	$voto = get_post_meta(get_the_ID(),mk_get_option("result"),true);
	if ($voto > 0)
		$votaux = 'positive';
	else if ($voto < 0)
		$votaux = 'negative';
	else
		$votaux = 'neutral';

	$genre = wp_get_post_terms( get_the_ID() , 'public_agent_genre');

	if (is_array($genre)) {
		$genre = $genre[0];
		$genre_slug = $genre->slug;
	}
	?>
	<div class=" et_pb_row pa_c2a <?php echo $votaux; ?>">

		<div class="et_pb_column et_pb_column_4_4">

			<div class="pa_c2a_text">
				<?php
				if ($voto > 0)
				{
					?>
					<p>Ess<?php echo $genre_slug=='feminino'?'a':'e'; ?> deputad<?php echo $genre_slug=='feminino'?'a':'o'; ?> apoia a investigação! </p>
					<p>Use as informações abaixo para divulgar sua posição.</p><p></p>
					<?php
				}

				else if ($voto < 0)
				{
					?>
					<p>Ess<?php echo $genre_slug=='feminino'?'a':'e'; ?> deputad<?php echo $genre_slug=='feminino'?'a':'o'; ?> quer safar Temer! </p>
					<p>Use as informações abaixo para incomodá-l<?php echo $genre_slug=='feminino'?'a':'o'; ?>, fazendo</p><p>pressão nas redes sociais e divulgando sua posição.</p>
					<?php
				}
				else
				{
					?>


					<p>Ess<?php echo $genre_slug=='feminino'?'a':'e'; ?> deputad<?php echo $genre_slug=='feminino'?'a':'o'; ?> está indecis<?php echo $genre_slug=='feminino'?'a':'o'; ?>!</p>
					<p>Use as informações abaixo para pressioná-l<?php echo $genre_slug=='feminino'?'a':'o'; ?> </p><p>e votar a favor da investigação.</p>
					<?php
				}
				?>

			</div>
		</div>
	</div>
	<div id="main-content">
		<div class="container">
			<div id="content-area" class="clearfix">


				<div id="post-<?php the_ID(); ?>" <?php post_class( " makepressure_grid makepressure_grid_item ".$votaux ); ?>>

					<?php

					$thumb = '';

					$width = 'on' === $fullwidth ?  150 : 400;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height = 'on' === $fullwidth ?  200 : 284;
					$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
					$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
					$titletext = get_the_title();

					$cargo = wp_get_post_terms( get_the_ID() , 'public_agent_job' ) ? wp_get_post_terms(  get_the_ID(), 'public_agent_job', true) : '';
					$cargo = isset($cargo[0]) ? $cargo[0] : '';
					if($cargo):
						?>
					<a href="<?php esc_url( the_permalink() ); ?>">
						<?php if(has_post_thumbnail()) : ?>
						<?php the_post_thumbnail(array(175,175), array('class' => 'makepressure_' . $cargo->slug . ' makepressure_post_main_image')); ?>
					<?php endif; ?>
				</a>
				<?php
				endif;
				if ( 'on' !== $fullwidth ) :

					$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
					)
				: '';
				?>
			</span>
		<?php endif; ?>
	</a>

	<div class="makepressure_label">

		<h2 class="makepressure_title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>


		<?php
			  //pre get categories
		$state = wp_get_post_terms( get_the_ID() , 'public_agent_state');
		$party = wp_get_post_terms( get_the_ID() , 'public_agent_party');
			  //$category = wp_get_post_terms( get_the_ID() , 'category')[0];
		?>
		<strong class="makepressure_upper">
			<?php if ($state[0]->slug): ?>
			<?php echo $state[0]->slug; ?>
		<?php else: ?>
		<br>
	<?php endif; ?>

	<?php if (isset($party[0]->slug)): ?>
	<?php echo ' / '; ?>
	<?php echo $party[0]->slug; ?>
<?php else: ?>
	<br>
<?php endif; ?>
</strong>
</div>

<?php wp_divi_get_share_buttons(); ?>

</div>
<?php
$state = wp_get_post_terms( get_the_ID() , 'public_agent_state');
$job = wp_get_post_terms( get_the_ID() , 'public_agent_job');
$party = wp_get_post_terms( get_the_ID() , 'public_agent_party');
$commissions = wp_get_post_terms( get_the_ID() , 'public_agent_commission');
$phone = get_post_meta(  get_the_ID(), 'public_agent_phone', true);
$email = get_post_meta(  get_the_ID(), "public_agent_email", true);

?><div class="public_agent_wrapper">
<div class="public_agent_image">
	<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/identidade.png">
</div>
<div class="public_agent_info">
	<h1><?php the_title(); ?><br/></h1>
	<span><?php echo strtoupper("PARTIDO: ".$party[0]->slug."/".$state[0]->slug); ?><br/></span>
	<span><?php echo "CARGO: ".$job[0]->name; ?><br/><br/></span>
	<span class="phone"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/telefone.png">(61) <?php echo $phone; ?><br/></span>
	<span class="email"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/email.png"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></span></a><br/>
</div>
</div>
</div> <!-- #left-area -->
<?php the_widget("et_makepressure_result",array("single"=>true,"campaign"=>mk_get_option("result"))); ?>
</div> <!-- #content-area -->
</div> <!-- .container -->
</div> <!-- #main-content -->
<?php endwhile; ?>
<?php get_footer(); ?>
