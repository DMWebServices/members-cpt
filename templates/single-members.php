<?php get_header(); ?>

<div class="dsm-members-single-container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <article class="members-single-page" id="post-<?php the_ID(); ?>" <?php post_class('member-single'); ?>>
                       
            <?php if (has_post_thumbnail()) : ?>
                <div class="member-photo-featured">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Members Single Page Content -->
             <div class="dsm-members-single-content-container">
                <div class="dsm-members-single-content">
                    <h1 class="dsm-members-single-title"><?php the_title(); ?></h1>                    
                    <?php
                    $bio = get_post_meta(get_the_ID(), 'member_bio', true);
                    if (!empty($bio)) : ?>
                        <p><?php echo esc_html($bio); ?></a></p>
                    <?php endif; ?>  
                </div>        
                <!-- Contact Info -->
                <div class="dsm_members-contact-info">
                    <h3 class="dsm-contact-info--title">Contact</h3>
                    <?php
                    $email = get_post_meta(get_the_ID(), 'member_email', true);
                    if (!empty($email)) : ?>
                        <p><i class="fa-solid fa-envelope"></i><a class="contact-links" href="mailto:<?php echo esc_attr($email); ?>">
                            <?php echo esc_html($email); ?></a>
                        </p>
                    <?php endif; ?>                 
                        
                    <?PHP
                    $phone = get_post_meta(get_the_ID(), 'member_phone', true);
                    if (!empty($phone)) : ?>
                        <p><i class="fa-solid fa-phone"></i><a class="contact-links" href="tel:<?php echo esc_attr($phone); ?>">
                            <?php echo esc_html($phone); ?></a>
                        </p>
                    <?php endif; ?>                   
                </div>       
            </div>   
        </article>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
