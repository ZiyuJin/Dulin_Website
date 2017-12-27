<?php
get_header();
global $wdwt_front;
?>
</header>

<div class="homeMainSection">
	<?php if( 'posts' == get_option( 'show_on_front' ) ){ ?>
		<div id="top_posts_out">
			<?php Business_elite_frontend_functions:: top_posts(); ?>
		</div>
	<?php } ?>

	<div id="category_tabs_sec" class="container wd_tabs_dynamic">
		<?php if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
			<aside id="sidebar1">
				<div class="sidebar-container">
					<?php  dynamic_sidebar( 'sidebar-1' ); 	?>
					<div class="clear"> </div>
				</div>
			</aside>
		<?php } ?>

		<div id="content" class="blog">
			<?php
			if( 'posts' == get_option( 'show_on_front' ) ){
				Business_elite_frontend_functions:: category_tab();
			}
			elseif('page' == get_option( 'show_on_front' )){
				Business_elite_frontend_functions:: content_for_home();
			}
			?>
		</div>

		<?php if ( is_active_sidebar( 'sidebar-2' ) ) { ?>
			<aside id="sidebar2">
				<div class="sidebar-container">
					<?php  dynamic_sidebar( 'sidebar-2' ); 	?>
					<div class="clear"></div>
				</div>
			</aside>
		<?php } ?>
		<div class="clear"></div>
	</div>


<?php if( 'posts' == get_option( 'show_on_front' ) ) { ?>
	
	<div id="featured_sec">
		<?php Business_elite_frontend_functions:: home_featured_post(); ?>
	</div>
	
	<div id="blog_home_out">
		<?php Business_elite_frontend_functions:: content_posts(); ?>
	</div>
	
	<div id="portfolio_home_out">
		<?php Business_elite_frontend_functions:: portfolio_home(); ?>
	</div>
	
	<div id="contact_us_sec">
		<?php Business_elite_frontend_functions:: contact_us(); ?>
	</div>	

<?php } ?>

	<div class="clear"></div>
</div>
<?php
get_footer();
