<?php
/**
 * Template Name: Titleless
 * Description: Page template without a title.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <div class="entry-content">
                    
                    <section class="landing-intro landing-separator">
                        <?php the_content(); ?>
                    </section>

                </div>
            </article>

        <?php endwhile; // End of the loop. ?>

    </main>
</div>

<?php get_footer(); ?>