<?php
/**
 * Added SideNav Sidebar state functionality
 * to save the last state of the SideNav Sidebar on a cookie.
 *
 * @return void
 */
add_action( 'init', 'thrive_set_sidenav_cookie' );

function thrive_set_sidenav_cookie() {

    $sidenav_cookie = '';
    $set_sidenav_cookie = '';
    $is_sidenavigation_closed = filter_input( INPUT_COOKIE, 'isSideNavSidebarClose', FILTER_SANITIZE_SPECIAL_CHARS );
    $sidenav_cookie = $is_sidenavigation_closed;
    $set_sidenav_cookie = apply_filters( 'thrive_set_sidenav_state', $sidenav_cookie );

    $sidenav_cookie_mobile = '';
    $set_sidenav_cookie_mobile = '';
    $is_sidenavigation_closed_mobile = filter_input( INPUT_COOKIE, 'isSideNavSidebarMobileClose', FILTER_SANITIZE_SPECIAL_CHARS );

    if( empty( $is_sidenavigation_closed_mobile ) ) {
        $is_sidenavigation_closed_mobile = 'mobile_close';
    }
    
    $sidenav_cookie_mobile = $is_sidenavigation_closed_mobile;
    $set_sidenav_cookie_mobile = apply_filters( 'thrive_set_sidenav_mobile_state', $sidenav_cookie_mobile );


    setcookie( 'isSideNavSidebarClose', $set_sidenav_cookie, time() + (86400 * 30), "/" );

    if( 'mobile_close' === $set_sidenav_cookie_mobile ) {
        setcookie( 'isSideNavSidebarClose', $set_sidenav_cookie_mobile, time() + (86400 * 30), "/" );
    }

    return;
}

/**
 * Gets the cookie for the Sidenav sidebar
 * and returns the class for the Sidenav sidebar.
 *
 * @return string $sidenav_state the class for the Sidenav sidebar.
 */
function thrive_get_sidenav_state() {

    $sidenav_state = '';
    $sidenav_cookie = '';

    $is_sidenavigation_closed = filter_input( INPUT_COOKIE, 'isSideNavSidebarClose', FILTER_SANITIZE_SPECIAL_CHARS );

    $sidenav_cookie = $is_sidenavigation_closed;

    if ( 'close' === $sidenav_cookie ) {
        $sidenav_state = 'toggled';
    }

    if ( 'mobile_close' === $sidenav_cookie ) {
        $sidenav_state = '';
    }

    return apply_filters( 'thrive_get_sidenav_state', $sidenav_state );
}
