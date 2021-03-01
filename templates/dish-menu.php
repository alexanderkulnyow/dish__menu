<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package arena
 */

get_header();
?>

    <div id="primary" class="content-area" style="margin-bottom: 30px;">

        <main id="main" class="site-main container container-menu">
			<?php
			/**
			 * Hook: woocommerce_before_main_content.
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 * @hooked WC_Structured_Data::generate_website_data() - 30
			 */
			//		do_action( 'woocommerce_before_main_content' );
			if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				woocommerce_breadcrumb();
			}

			?>
            <header class="page-header">
                <div class="h1_with_file">
				<?php if ( is_category() ) {
					echo '<h1 class="" style="margin-top: 20px; margin-bottom: 20px;"> ' . get_queried_object()->name . '</h1>';
				}
				if (is_category('banketnoe-menu')) {
				    ?>

					<div class="">
                        <button class="buton-galery h1_with_button">
                            <a href="<?php echo nl2br( esc_html( get_theme_mod( 'file_choiser' ) ) )?>">Банкетное меню</a>
                        </button>
                    </div>

                <?php
                }
				?>
                </div>
            </header><!-- .page-header -->
            <div class="col-12 order-md-0 order-1">
                <nav class="d-block">
                    <ul class="controls menu_controls">
						<?php
						if ( is_category() ) {
							$current_cat_id = get_queried_object()->term_id;
							$args           = array(
								'child_of'     => $current_cat_id,
								'orderby'      => 'name',
								'order'        => 'ASC',
								'hide_empty'   => 1,
								'hierarchical' => 0,
								'number'       => 0,
							);
							$categories     = get_categories( $args );
							if ( $categories ) {
								foreach ( $categories as $category ) {
									echo '<li data-filter=".' . $category->slug . '"> ' . $category->name . '</li>';
								}
							}
						}
						?>
                    </ul>
                </nav>
            </div>

            <div class="row menu__container">
				<?php
				$args  = array(
					'numberposts'      => - 1,
					'category_name'    => get_the_category()->slug,
					'orderby'          => 'date',
					'order'            => 'ASC',
					'include'          => array(),
					'exclude'          => array(),
					'meta_key'         => '',
					'meta_value'       => '',
					'post_type'        => 'dish-menu',
					'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
				);
				$dishes = get_posts( $args );
				foreach ( $dishes as $post ) {
					setup_postdata( $post );
					$categories  = get_the_category();
					$slugs       = wp_list_pluck( $categories, 'slug' );
					$class_names = join( ' ', $slugs );
					?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mix <?php if ( $class_names ) {
						echo ' ' . $class_names;
					} ?> ">
                        <div class="card col-12">
                            <div class="card__header ">
                                <div class="embed-responsive embed-responsive-1by1">
									<?php if ( has_post_thumbnail() ) {
										$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
										?>
                                        <img class="photo-img-top img-fluid embed-responsive-item"
                                             src="<?php echo $thumb[0] ?>" alt="Ресторан-клуб Танцы" title="<?php the_title_attribute( $args ); ?>"/>
										<?php
									} ?>
                                </div>

                            </div>
                            <div class="card-body card__body dish__body">
								<?php the_title( '<h2 class="product_title entry-title dish__title">', '</h2>' ); ?>
                                <div class="dish__description"><?php the_content(); ?> </div>
                                <div class="dish__footer">
                                    <div class="card__price dish__price">
                                        <p><?php echo get_post_meta( $post->ID, 'menu-weight', true ); ?> г</p>
                                        <p><?php echo get_post_meta( $post->ID, 'menu-cost', true ); ?> BYN</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				}
				wp_reset_postdata(); ?>
            </div>


        </main><!-- #main -->
    </div><!-- #primary -->
    <script>
        jQuery(document).ready(function ($) {
            // микситап
            $(function () {
                let containerEl = document.querySelector('.menu__container');
                let mixer = mixitup(containerEl, {
                    controls: {
                        // toggleDefault: 'none'
                    },
                    animation: {
                        duration: 250,
                        // easing: 'ease-in-out'
                    }
                });
            })
        });

    </script>
<?php
get_footer();
