<?php
/* Social Icons */
$social_icons_switch = ot_get_option('social_icons_switch');
$social_icons_inverted = ot_get_option('social_icons_color');
$social_icons = ot_get_option('social_icons');

if(isset($social_icons) && !empty($social_icons)){ ?>
    <div class="social-icons">
        <ul>
            <?php foreach($social_icons as $icon): ?>

                <?php
                    //skip Google+ icon
                    if ($icon['social_icon_entry'] === 'google'){
                        continue;
                    }
                    printf('
                        <li class="%s">
                            <a href="%s" target="_blank">%s</a>
                        </li>',
                        (isset($icon['social_icon_entry'])) ? $icon['social_icon_entry'] : '',
                        (isset($icon['social_profile_link'])) ? $icon['social_profile_link'] : '#',
                        onecom_svg_social_icons(((isset($icon['social_icon_entry'])) ? $icon['social_icon_entry'] : ''))
                    );
                ?>

            <?php endforeach; ?>
        </ul>
    </div>
<?php } ?>