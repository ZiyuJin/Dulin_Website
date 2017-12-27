<?php

get_header();
global $wdwt_front;
$blog_style = $wdwt_front->blog_style();
$grab_image = $wdwt_front->get_param('grab_image');
$single_title_bg = $wdwt_front->get_param('single_title_bg', array(), true);
?>

</header>
<div class="<?php echo $single_title_bg ? 'before_blog_2' : 'before_blog_1'; ?> archive_back_phone">

  <?php
  if (have_posts()) {
    if (is_category()) { ?>
      <h1
        class="styledHeading"><?php _e('Archive For The ', "business-elite"); ?>&ldquo;<?php single_cat_title(); ?>&rdquo; <?php _e('Category', "business-elite"); ?></h1>
      <?php
    } elseif (is_tag()) { ?>
      <h1 class="styledHeading"><?php _e('Posts Tagged ', "business-elite"); ?>&ldquo;<?php single_tag_title(); ?>&rdquo;</h1>
      <?php
    } elseif (is_day()) { ?>
      <h1
        class="styledHeading"><?php _e('Archive For ', "business-elite"); ?><?php the_time(get_option('date_format')); ?></h1>
      <?php
    } elseif (is_month()) { ?>
      <h1
        class="styledHeading"><?php _e('Archive For ', "business-elite"); ?><?php the_time(get_option('date_format')); ?></h1>
      <?php
    } elseif (is_year()) { ?>
      <h1
        class="styledHeading"><?php _e('Archive For ', "business-elite"); ?><?php the_time(get_option('date_format')); ?></h1>
      <?php
    } elseif (is_author()) { ?>
      <h1 class="styledHeading"><?php _e('Author Archive', "business-elite"); ?></h1>
    <?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
      <h1 class="styledHeading"><?php _e('Blog Archives', "business-elite"); ?></h1>
      <?php
    }
  } else {

    ?>
    <h1 class="styledHeading"><?php _e('Not Found', "business-elite"); ?></h1>
    <?php
  } ?>


</div>
<?php if ($single_title_bg) { ?>
  <div class="before_blog"></div>
<?php } ?>

<div class="container">
  <?php
  if (is_active_sidebar('sidebar-1')) { ?>
    <aside id="sidebar1">
      <div class="sidebar-container">
        <?php dynamic_sidebar('sidebar-1'); ?>
        <div class="clear"></div>
      </div>
    </aside>
  <?php } ?>

  <div id="blog" class="blog archive-page">

    <?php if (have_posts()) : $post = $posts[0]; ?>


      <?php while (have_posts()) : the_post(); ?>

        <div class="archive-page clear_tag hentry" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

          <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
            <?php
            if (has_post_thumbnail() || (Business_elite_frontend_functions::post_image_url() && $blog_style & $grab_image)) {
              ?>
              <div class="blog_img_container img_container unfixed size360">
                <?php echo Business_elite_frontend_functions::auto_thumbnail($grab_image); ?>
              </div>
              <?php
            } ?>
          </a>
          <div class="post clear_tag">
            <h2 class="archive-header">
              <a href="<?php the_permalink(); ?>" rel="bookmark"
                 title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
            </h2>
            <?php
            if ($blog_style) {
              the_excerpt();
            } else {
              the_content();
            } ?>
          </div>
          <?php if ($wdwt_front->get_param('date_enable')) { ?>
            <div class="entry-meta">
              <?php Business_elite_frontend_functions::posted_on_single();
              Business_elite_frontend_functions::entry_meta(); ?>
            </div>
          <?php } ?>
        </div>
      <?php endwhile; ?>

      <div class="page-navigation">
        <?php posts_nav_link(' ', '&laquo;Previous', 'Next &raquo;'); ?>
      </div>
    <?php else : ?>


      <p><?php _e('There are not posts belonging to this category or tag. Try searching below:', "business-elite"); ?></p>
      <div id="search-block-category"><?php get_search_form(); ?></div>

    <?php endif; ?>

    <?php $wdwt_front->bottom_advertisment(); ?>
  </div>

  <?php
  if (is_active_sidebar('sidebar-2')) { ?>
    <aside id="sidebar2" class="widget-area">
      <div class="sidebar-container">
        <?php dynamic_sidebar('sidebar-2'); ?>
        <div class="clear"></div>
      </div>
    </aside>
  <?php } ?>

  <div class="clear"></div>

  <?php wp_reset_query(); ?>

  <!--COMMENTS-->
  <?php if (comments_open()) { ?>
    <div class="comments-template">
      <?php comments_template(); ?>
    </div>
  <?php } ?>
</div>

<?php get_footer(); ?>
