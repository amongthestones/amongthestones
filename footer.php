<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Simppeli
 */

?>

	</div><!-- #content -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="site-info">
	Empowered by <a href="http://wordpress.org/">WordPress</a><br>
    <a href="<?php echo esc_url( get_feed_link() ); ?>">RSS</a>
    · <a href="/contact">Contact</a> · 
	<a href="https://github.com/amongthestones">GitHub</a> · 
	<a href="amongthestones.com/verify">Verify</a><br>
	<em>‘Hasbiyallāh. ¿Y ahora qué?</em>
    </div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>