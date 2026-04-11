<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background flex flex-col items-center justify-center w-full z-10 relative">
  <div class="absolute top-0 left-0 z-0 w-full h-[500px] [mask-image:linear-gradient(to_top,transparent_25%,black_95%)]">
    <div class="absolute top-0 left-0 size-full" style="background-image: radial-gradient(#6B7280 1px, transparent 1px); background-size: 10px 10px; opacity: 0.5;"></div>
  </div>
  <div class="text-center flex flex-col gap-4 max-w-xs mx-auto relative z-10">
    <h1 class="text-8xl font-mono font-bold drop-shadow-lg text-primary">
      404
    </h1>
    <p class="text-muted-foreground text-base leading-relaxed text-center tracking-tight text-balance">
      <?php esc_html_e( 'Sorry, we couldn\'t find the page you\'re looking for. The page might have been moved, deleted, or you entered the wrong URL.', 'titancore' ); ?>
    </p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 w-full rounded-lg h-9 drop-shadow-lg">
      <?php esc_html_e( 'Back to Home', 'titancore' ); ?>
    </a>
  </div>
</main>

<?php
get_footer();
