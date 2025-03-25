<?php get_header(); ?>

<div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <article class="members-single-page" id="post-<?php the_ID(); ?>" <?php post_class('member-single'); ?>>
            <h1 class="members-title"><?php the_title(); ?></h1>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="member-photo">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <?php
            $bio = get_post_meta(get_the_ID(), 'member_bio', true);
            if (!empty($bio)) : ?>
                <p><?php echo esc_html($bio); ?></a></p>
            <?php endif; ?>  

            <?php
            $email = get_post_meta(get_the_ID(), 'member_email', true);
            if (!empty($email)) : ?>
                <p><i class="fa-solid fa-envelope"></i><a href="mailto:<?php echo esc_attr($email); ?>">
                    <?php echo esc_html($email); ?></a>
                </p>
            <?php endif; ?>                 
            
            <?PHP
            $phone = get_post_meta(get_the_ID(), 'member_phone', true);
            if (!empty($phone)) : ?>
                <p><i class="fa-solid fa-phone"></i><a href="tel:<?php echo esc_attr($phone); ?>">
                    <?php echo esc_html($phone); ?></a>
                </p>
            <?php endif; ?>             
        </article>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
