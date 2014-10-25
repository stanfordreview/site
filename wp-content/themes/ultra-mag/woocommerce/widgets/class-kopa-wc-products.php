<?php
/**
 * Kopa Woocommerce products widget
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Woocommerce_Products extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-product-list-widget', 'description' => __('Display a list of your most recent products on your site.', kopa_get_domain()));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('kopa_widget_woocommerce_products', __('Kopa Woocommerce Products', kopa_get_domain()), $widget_ops, $control_ops);

        add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
    }

    /**
     * get price html function
     * @access public
     * @param product object - $product
     * @return void
     */
    function get_price_html( $product ) {
        // check product type
        if ( 'variable' === $product->product_type ) {
            return $this->get_variable_price_html( $product );
        } elseif ( 'grouped' === $product->product_type ) {
            return $this->get_grouped_price_html( $product );
        }

        $price_html = '';

        if ( $product->price > 0 ) {
            
            if ( $product->is_on_sale() && isset( $product->regular_price ) ) {
                
                $price_html .= '<del><span class="price">'.woocommerce_price( $product->regular_price ).'</span></del>';

                $price_html .= '<span class="current-price">'.woocommerce_price( $product->get_price() ).'</span>';

                $sale_price = (int) $product->get_price();
                $regular_price = (int) $product->regular_price;

                if ( $regular_price > $sale_price ) {

                    $percentages_off = ( 1 - $sale_price / $regular_price ) * 100;
                    
                    if ( $percentages_off > 0 && $percentages_off < 100 ) {
                    
                        $price_html .= '<span class="saleoff">'.sprintf( '%.0f%% off', $percentages_off ).'</span>';
                    
                    }
                }

            } else {
                
                $price_html .= '<span class="current-price">'.woocommerce_price( $product->get_price() ).'</span>';
            
            }
        } elseif ( $product->price === '' ) {

            $price_html = '';
        
        } elseif ( $product->price == 0 ) {
        
            if ( $product->is_on_sale() && isset( $product->regular_price ) ) {
                
                $price_html .= '<del><span class="price">'.woocommerce_price( $product->regular_price ).'</span></del>';
                $price_html .= '<span class="current-price">'.__( 'Free', kopa_get_domain() ).'</span>';

            } else {
                
                $price_html .= '<span class="current-price">'.__( 'Free', kopa_get_domain() ).'</span>';
            
            }

        }

        return $price_html;
    }

    /**
     * get price html of variable product function
     * @access public
     * @param product object - $product
     * @return void
     */
    function get_variable_price_html( $product ) {
        $price = '';

        // Get the price
        if ( $product->price > 0 ) {

            if ( $product->is_on_sale() && isset( $product->min_variation_price ) && $product->min_variation_regular_price !== $product->get_price() ) {

                if ( ! $product->min_variation_price || $product->min_variation_price !== $product->max_variation_price )
                    $price .= $product->get_price_html_from_text();

                $price .= '<del><span class="price">'.woocommerce_price( $product->min_variation_regular_price ).'</span></del>';

                $price .= '<span class="current-price">'.woocommerce_price( $product->get_price() ).'</span>';

            } else {

                if ( $product->min_variation_price !== $product->max_variation_price )
                    $price .= $product->get_price_html_from_text();
                $price .= '<span class="current-price">'.woocommerce_price( $product->get_price() ).'</span>';

            }

        } elseif ( $product->price === '' ) {

            $price = '';

        } elseif ( $product->price == 0 ) {

            if ( $product->is_on_sale() && isset( $product->min_variation_regular_price ) && $product->min_variation_regular_price !== $product->get_price() ) {

                if ( $product->min_variation_price !== $product->max_variation_price )
                    $price .= $product->get_price_html_from_text();

                $price .= '<span class="current-price">'.__( 'Free!', kopa_get_domain() ).'</span>';

            } else {

                if ( $product->min_variation_price !== $product->max_variation_price )
                    $price .= $product->get_price_html_from_text();

                $price .= '<span class="current-price">'.__( 'Free!', kopa_get_domain() ).'</span>';

            }

        }

        return $price;
    }

    /**
     * get price html of grouped product function
     * @access public
     * @param product object - $product
     * @return void
     */
    function get_grouped_price_html( $product ) {
        $child_prices = array();
        $price = '';

        foreach ( $product->get_children() as $child_id )
            $child_prices[] = get_post_meta( $child_id, '_price', true );

        $child_prices = array_unique( $child_prices );

        if ( ! empty( $child_prices ) ) {
            $min_price = min( $child_prices );
        } else {
            $min_price = '';
        }

        if ( sizeof( $child_prices ) > 1 ) $price .= $product->get_price_html_from_text();

        $price .= '<span class="current-price">'.woocommerce_price( $min_price ).'</span>';

        return $price;
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget($args, $instance) {
        global $woocommerce;

        $cache = wp_cache_get('kopa_widget_woocommerce_products', 'widget');

        if ( !is_array($cache) ) $cache = array();

        if ( isset($cache[$args['widget_id']]) ) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('New Products', kopa_get_domain() ) : $instance['title'], $instance, $this->id_base);
        if ( !$number = (int) $instance['number'] )
            $number = 10;
        else if ( $number < 1 )
            $number = 1;
        else if ( $number > 15 )
            $number = 15;

        $show_variations = $instance['show_variations'] ? '1' : '0';

        $query_args = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');

        $query_args['meta_query'] = array();

        if ( $show_variations == '0' ) {
            $query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
            $query_args['parent'] = '0';
        }

        $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
        $query_args['meta_query']   = array_filter( $query_args['meta_query'] );

        $r = new WP_Query($query_args);

        if ( $r->have_posts() ) {

            echo $before_widget;

            if ( $title )
                echo $before_title . $title . $after_title;

            echo '<ul class="clearfix">';

            while ( $r->have_posts()) {
                $r->the_post();
                global $product;

                // get add to cart link markup
                ob_start();
                woocommerce_template_loop_add_to_cart();
                $add_to_cart_link = ob_get_clean();

                echo '<li>
                    <article class="product-item">
                        <div class="product-thumb">
                            <a href="' . get_permalink() . '">
                                ' . ( has_post_thumbnail() ? get_the_post_thumbnail( $r->post->ID, 'kopa-article-list-size' ) : woocommerce_placeholder_img( 'shop_thumbnail' ) ) . '
                            </a>
                        </div>
                        <div class="product-content">
                            <h6 class="product-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h6>
                            <footer class="clearfix">'.$this->get_price_html( $product ).'</footer>' . $add_to_cart_link .
                        '</div>
                    </article>
                </li>';
            }

            echo '</ul>';

            echo $after_widget;
        }

        wp_reset_postdata();

        $content = ob_get_clean();

        if ( isset( $args['widget_id'] ) ) $cache[$args['widget_id']] = $content;

        echo $content;

        wp_cache_set('kopa_widget_woocommerce_products', $cache, 'widget');
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_variations'] = !empty($new_instance['show_variations']) ? 1 : 0;

        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['kopa_widget_woocommerce_products']) ) delete_option('kopa_widget_woocommerce_products');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('kopa_widget_woocommerce_products', 'widget');
    }

    /**
     * form function.
     *
     * @see WP_Widget->form
     * @access public
     * @param array $instance
     * @return void
     */
    function form( $instance ) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
            $number = 5;

        $show_variations = isset( $instance['show_variations'] ) ? (bool) $instance['show_variations'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', kopa_get_domain() ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of products to show:', kopa_get_domain() ); ?></label>
        <input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>

        <p><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_variations') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_variations') ); ?>"<?php checked( $show_variations ); ?> />
        <label for="<?php echo $this->get_field_id('show_variations'); ?>"><?php _e( 'Show hidden product variations', kopa_get_domain() ); ?></label></p>

        <?php
    }
}