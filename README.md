# WP Foundation Walker

A custom menu walker to make the standard WordPress Menu system compatible with the Foundation TopBar. (http://foundation.zurb.com/docs/components/topbar.html)

## Usage

You can drop the WP_Foundation_Walker.php file into the /mu-plugins/ folder or add it to your theme files and include it manually.

### Example

Then you can add the Top Bar Menu to your theme using code similar to this:

```
<nav class="top-bar" data-topbar data-options="mobile_show_parent_link: true" role="navigation">

    <ul class="title-area">
        <li class="name">
            <h1><a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo esc_html( bloginfo( 'name' ) ); ?></a></h1>
        </li>
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">
        <!-- Right Nav Section -->
        <?php
        wp_nav_menu( array(
            'container' => false,
            'menu_class' => 'right',
            'theme_location' => 'topbar-right',
            'walker' => new WP_Foundation_TopBar(),
        ) );
        ?>

        <!-- Left Nav Section -->
        <?php
        wp_nav_menu( array(
            'container' => false,
            'menu_class' => 'left',
            'theme_location' => 'topbar-left',
            'walker' => new WP_Foundation_TopBar(),
        ) );
        ?>
    </section>

</nav>
```

Notes:
* Remember to edit the 'theme_location' value with menu areas relevant to your theme.
* The 'container' value must be false or a container element is added around the menu.