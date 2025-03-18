<?php get_header(); ?>

<div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('member-single'); ?>>
            <h1><?php the_title(); ?></h1>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="member-photo">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <p><strong>Bio:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'member_bio', true)); ?></p>
            <p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr(get_post_meta(get_the_ID(), 'member_email', true)); ?>">
                <?php echo esc_html(get_post_meta(get_the_ID(), 'member_email', true)); ?></a>
            </p>
            <p><strong>Phone:</strong> <a href="tel:<?php echo esc_attr(get_post_meta(get_the_ID(), 'member_phone', true)); ?>">
                <?php echo esc_html(get_post_meta(get_the_ID(), 'member_phone', true)); ?></a>
            </p>

            <?php the_content(); ?>
        </article>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
