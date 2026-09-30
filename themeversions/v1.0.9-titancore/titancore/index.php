<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
	<?php get_template_part( 'template-parts/background', 'grid' ); ?>

	<div class="p-6 border-b border-border flex flex-col gap-6 min-h-[200px] justify-center relative z-10">
		<div class="max-w-7xl mx-auto w-full">
			<div class="flex flex-col gap-2">
				<h1 class="font-medium text-3xl md:text-4xl tracking-tighter">
					<?php esc_html_e( 'Latest Posts', 'titancore' ); ?>
				</h1>
				<p class="text-muted-foreground text-sm md:text-base lg:text-lg">
					<?php esc_html_e( 'Browse the most recent content from this site.', 'titancore' ); ?>
				</p>
			</div>
		</div>
	</div>

	<div class="max-w-7xl mx-auto w-full px-6 lg:px-0">
		<?php if ( have_posts() ) : ?>
			<?php get_template_part( 'template-parts/loop', 'container' ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
