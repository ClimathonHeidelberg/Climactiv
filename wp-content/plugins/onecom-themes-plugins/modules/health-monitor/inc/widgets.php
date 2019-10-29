<?php
/**
 * Add widget to the dashboard to display the site health status.
 */
add_action('wp_dashboard_setup', 'osch_widget_cb');
add_action('wp_network_dashboard_setup', 'osch_widget_cb');
const WID = 'ocsh_dashboard_widget';
function osch_widget_cb()
{
    wp_add_dashboard_widget(
        WID,
        __('One.com Health Monitor', 'onecom-wp'),
        'ocsh_widget_cb'
    );

    global $wp_meta_boxes;
    if(isset($wp_meta_boxes['dashboard'])){
        $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
        $example_widget_backup = array(WID => $normal_dashboard[WID]);
        unset($normal_dashboard[WID]);
        $sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }
}

function ocsh_widget_cb()
{
    $url = menu_page_url( 'onecom-wp-health-monitor', false );
    $site_scan_transient = get_site_transient('ocsh_site_scan_result');
    $site_scan_result = oc_sh_calculate_score($site_scan_transient);
    if ( ! $site_scan_result ){
        echo '<p><a class="button button-primary" href="'.$url.'">'.__('Scan now', 'onecom-wp').'</a></p>';
        return;
    }
    $color = '#4ab865';
    if ($site_scan_result['score'] < 85 && $site_scan_result['score'] >=50){
        $color = '#ffb900';
    }elseif($site_scan_result['score'] < 50){
        $color = '#dc3232';
    }
    $time_format = get_option( 'time_format' );
    $date_format = get_option( 'date_format' );
    echo '<p>'.__('Health Monitor score is', 'onecom-wp').' <strong style="color:'.$color.'">'.round($site_scan_result['score']).'%</strong> ('.__('Last checked on', 'onecom-wp').' '.date_i18n($date_format.' '.$time_format, $site_scan_result['time']).')</p><p><a class="button button" href="'.$url.'">'.__('Scan again', 'onecom-wp').'</a></p>';
}
