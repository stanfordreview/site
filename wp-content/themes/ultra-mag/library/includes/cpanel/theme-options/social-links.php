<div id="tab-social-links" class="kopa-content-box tab-content tab-content-1">    

    <?php
    /* Social links target */
    ?>
    <div class="kopa-box-head">
        <i class="icon-hand-right"></i>
        <span class="kopa-section-title"><?php _e('Social Links Target', kopa_get_domain()); ?></span>
    </div><!--kopa-box-head-->

    <div class="kopa-box-body">
        <div class="kopa-element-box kopa-theme-options">
            <span class="kopa-component-title"><?php _e('Social Links Target', kopa_get_domain()); ?></span>            
            <?php
            $kopa_social_link_targets = array(
                '_self'  => __('Opens the linked document in the same frame as it was clicked', kopa_get_domain()),
                '_blank' => __('Opens the linked document in a new window or tab', kopa_get_domain()),
            );
            $kopa_social_link_target_name = "kopa_theme_options_social_link_target";
            foreach ($kopa_social_link_targets as $value => $label) {
                $kopa_target_id = $kopa_social_link_target_name . "_{$value}";
                ?>
                <label  for="<?php echo $kopa_target_id; ?>" class="kopa-label-for-radio-button"><input type="radio" value="<?php echo esc_attr( $value ); ?>" id="<?php echo $kopa_target_id; ?>" name="<?php echo $kopa_social_link_target_name; ?>" <?php echo ($value == get_option($kopa_social_link_target_name, '_self')) ? 'checked="checked"' : ''; ?>><?php echo $label; ?></label>
                <?php
            } // end foreach
            ?>
        </div>
    </div>

    <?php 
    /**
     * Social Links
     */
    ?> 
    <div class="kopa-box-head">
        <i class="icon-hand-right"></i>
        <span class="kopa-section-title"><?php _e('Social Links', kopa_get_domain()); ?></span>
    </div><!--kopa-box-head-->

    <div class="kopa-box-body">

        <?php
        $social_links = array(
            array(
                'title' => __( 'RSS URL', kopa_get_domain() ),
                'slug'  => 'rss',
                'desc'  => __('Display the RSS feed button with the default RSS feed or enter a custom feed below. <br><code>Enter <b>"HIDE"</b> if you want to hide it</code>', kopa_get_domain()),
            ),
            array(
                'title' => __( 'Facebook URL', kopa_get_domain() ),
                'slug'  => 'facebook',
                'desc'  => '',
            ),
            
            array(
                'title' => __( 'Twitter URL', kopa_get_domain() ),
                'slug'  => 'twitter',
                'desc'  => '',
            ),
            array(
                'title' => __( 'Google Plus URL', kopa_get_domain() ),
                'slug'  => 'gplus',
                'desc'  => '',
            ),
            array(
                'title' => __( 'Dribbble URL', kopa_get_domain() ),
                'slug'  => 'dribbble',
                'desc'  => '',
            ),
            array(
                'title' => __( 'Linkedin URL', kopa_get_domain() ),
                'slug'  => 'linkedin',
                'desc'  => '',
            ),
        );

        foreach ( $social_links as $index => $item ) { 
            $slug = 'kopa_theme_options_social_links_' . $item['slug'] . '_url';
            ?>
            <div class="kopa-element-box kopa-theme-options">
                <span class="kopa-component-title"><?php echo $item['title']; ?></span>
                <p class="kopa-desc"><?php echo $item['desc']; ?></p>
                <input type="text" value="<?php echo get_option( $slug ); ?>" id="<?php echo $slug; ?>" name="<?php echo $slug; ?>">
            </div><!--kopa-element-box-->
        <?php } // end foreach ?>
    </div>
</div>
