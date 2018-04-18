<?php

if ( ! defined( 'ABSPATH' ) ) die();

if ( ! function_exists( 'thrive_user_nav' ) ) {

	function thrive_user_nav() {

		if ( !function_exists('buddypress') )
		{
			return;
		}

		if ( !is_user_logged_in() ) { ?>

			<div id="user-nav-user-action" class="pull-right">

				<a title="<?php _e( 'Sign-in to your account', 'thrive' ); ?>" href="<?php echo esc_url( wp_login_url() ); ?>" class="button">
					<?php _e( 'Sign-in', 'thrive' ); ?>
				</a>

                <?php if ( get_option( 'users_can_register' ) ) { ?>
    				<a title="<?php _e( 'Create account to get started', 'thrive' ); ?>" href="<?php echo esc_url( wp_registration_url() ); ?>" class="button">
    					<?php _e( 'Register', 'thrive' ); ?>
    				</a>
                <?php } ?>

			</div>

			<div class="clearfix"></div>

			<?php

		} else {

			$message_notification = array();

			$profile_notification = array();

			?>
			<?php // Personal notifications ?>

			<?php $user_link = bp_loggedin_user_domain(); ?>

			<?php $user_notification_href = sprintf( "%s/profile", $user_link ); ?>

			<ul>

				<?php if ( function_exists( 'bp_notifications_get_notifications_for_user') ) { ?>

				<?php $notifications = bp_notifications_get_notifications_for_user( get_current_user_id() , 'string' ); ?>

				<?php $thrive_layout = get_theme_mod('thrive_layouts_customize', '2_columns'); ?>

				<?php if ( "1_column" === $thrive_layout ) { ?>
				<li class="item">
					<a class="hidden-sm hidden-xs" id="thrive-2-columns-search" href="<?php echo esc_url( get_search_link() );?>" alt="<?php _e("Search", "thrive"); ?>">
						<i class="material-icons md-24">search</i>
					</a>
				</li>
				<?php } ?>

				<li class="item">

					<a href="<?php echo esc_url( $user_notification_href ); ?>" title="<?php _e('See Notifications', 'thrive'); ?>">

						<?php if ( !empty( $notifications ) ) { ?>

							<span class="thrive-user-nav-bubble">
								<?php echo count( $notifications ); ?>
							</span>

						<?php } ?>

						<i class="material-icons md-24">face</i>
					</a>

					<?php if ( !empty( $notifications ) ) { ?>

						<div class="user-notifications">
							<?php if ( !empty( $notifications ) ) { ?>
							<ul class="user-notification-personal">
								<?php foreach ( $notifications as $notification ) { ?>
									<li><?php echo thrive_handle_empty_var( $notification ); ?></li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>

					<?php } ?>
				</li>
				<?php } ?>

				<?php // Unread notifications. ?>
				<?php $user_notification_list_href = sprintf( "%s/notifications", $user_link ); ?>

				<?php if ( function_exists( 'bp_notifications_get_unread_notification_count' ) ) { ?>

				<li class="item">

					<a href="<?php echo esc_url( $user_notification_list_href); ?>" title="<?php _e('See All Notifications', 'thrive'); ?>">

						<?php $unread_notifications = absint( bp_notifications_get_unread_notification_count( get_current_user_id() ) ); ?>

						<?php if ( 0 !== $unread_notifications ) { ?>
						<span class="thrive-user-nav-bubble">
							<?php echo intval( $unread_notifications ); ?>
						</span>
						<?php } ?>

						<i class="material-icons md-24">notifications</i>
					</a>


					<?php if ( 0 !== $unread_notifications ) { ?>

					<div class="user-notifications">
						<?php if ( bp_has_notifications() ) : ?>

                            <?php $user_notifications = thrive_bp_get_the_notifications_description(); ?>

                            <ul id="notifications-ul">
                                <?php foreach ( $user_notifications as $user_notification ) { ?>
                                    <li>
                                        <?php echo $user_notification; ?>
                                    </li>
                                <?php } ?>
							</ul>
						<?php endif; ?>
					</div>

					<?php } ?>

				</li>
				<?php } ?>
				<?php if ( function_exists('messages_get_unread_count') ) { ?>

				<li class="item">

					<?php $user_messages_link = sprintf("%s%s", $user_link, bp_get_messages_slug() ); ?>

					<a href="<?php echo esc_url( $user_messages_link ); ?>" title="<?php _e('Show Unread Messages', 'thrive'); ?>">
						<i class="material-icons md-24">email</i>
					</a>

					<?php $unread_message_count = absint( messages_get_unread_count() ); ?>

					<?php if ( 0 !== $unread_message_count ) { ?>

					<span class="thrive-user-nav-bubble">

						<?php echo intval( $unread_message_count ); ?>

					</span>

					<div id="message-notification" class="user-notifications">

						<div id="thrive-user-nav-messages-head">
							<?php _e('Messages', 'thrive'); ?>
						</div>
						<ul id="thrive-user-nav-messages">
                            <?php thrive_bp_users_messages(); ?>
						</ul>
						<div class="clearfix"></div>

						<div id="thrive-user-nav-messages-footer">

							<?php $messages_link = trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() . '/inbox' ); ?>

							<a href="<?php echo esc_url( $messages_link ); ?>" title="<?php _e('See All Messages', 'thrive'); ?>">
								<?php _e('See All Messages', 'thrive'); ?>
							</a>

						</div>
					</div><!--#message-notification-->
					<?php } ?>

				</li>
				<?php } ?>
				<?php $theme_mod_top_right_menu = get_theme_mod('thrive_top_right_bar', 'bp-menu'); ?>
				<?php if ( 'bp-menu' === $theme_mod_top_right_menu ) { ?>
					<?php // This is the buddypress generated top right bar menu. ?>
					<li class="item">
						<a href="#" title="" class="no-pd-right">
							<i class="material-icons md-24">menu</i>
						</a>
						<div class="user-notifications" id="navigation">
							<?php thrive_bp_navigation(); ?>
						</div>
					</li>
				<?php } else { ?>
					<?php // This is the top right bar menu. ?>
					<li class="item">
						<a href="#" title="" class="no-pd-right">
							<i class="material-icons md-24">menu</i>
						</a>
						<div class="user-notifications" id="navigation">
							<?php
								$nav = wp_nav_menu(
									array(
										'theme_location' => 'topbarmenu',
										'menu_id' => 'top-bar-menu',
										'echo' => false,
										'fallback_cb' => ''
									)
								);
							?>
							<?php if ( !empty( $nav ) ) { ?>
								<?php echo thrive_handle_empty_var( $nav ); ?>
							<?php } else { ?>
								<ul id="top-bar-menu">
									<li class="item">
										<a href="<?php echo esc_url( admin_url('nav-menus.php?action=locations') ); ?>">
											<i class="material-icons md-18">create</i>
											<?php _e("Edit 'Top Right Bar' Menu", 'thrive'); ?>
										</a>
									</li>
								</ul>
							<?php } ?>
						</div>
					</li>
				<?php } ?>
			</ul>
			<?php
			} // end else
		}

}  // End function exists thrive_user_nav.

add_action( 'thrive_after_bp_nav_menu', 'thrive_usernav_logout_link' );

function thrive_usernav_logout_link() {

	echo '<li id="sign-out" class="menu-parent"><a class="sign-out" href="'.esc_url(  wp_logout_url() ).'">'.__('Sign Out', 'thrive').'</a></li>';

}

function thrive_bp_nav_menu() {

	if ( !function_exists('buddypress') ) {
		return;
	}

	$loaded_components = buddypress()->loaded_components;

	$bp_nav_menu_items = bp_get_nav_menu_items();

	$parent_menus = array();

	$bp_menu = array();

	$transport_menu = array();

	foreach ( $loaded_components as $component_id => $component_value ) {

		$transport_menu[ $component_id ] = $component_value;

	}

	$transport_menu['xprofile'] = __('Profile', 'thrive');

	if ( function_exists( 'buddydrive_get_name' ) ) {

		$transport_menu['buddydrive'] = buddydrive_get_name();

	}

	$bp_doc_slug = get_option( 'bp-docs-slug', 'docs' );

	$bp_doc_name = get_option( 'bp-docs-user-tab-name', __( 'Docs', 'thrive' ) );

	if ( !empty( $bp_doc_slug ) ) {

		if ( !empty( $bp_doc_name ) ) {

			$transport_menu[$bp_doc_slug] = $bp_doc_name;

		}

	}

	// Get all the parent nav.
	foreach ( $bp_nav_menu_items as $nav_item ) {

		if ( $nav_item->parent === 0 ) {

			$parent_menus[ $nav_item->css_id ]['link'] = $nav_item->link;

			$parent_menus[ $nav_item->css_id ]['name'] = $nav_item->name;

		}
	}

	// Assign each sub nav to it's parent nav
	foreach ( $bp_nav_menu_items as $nav_item ) {

		if ( in_array( $nav_item->parent, array_keys( $parent_menus ) ) ) {

			if ( $nav_item->parent !== 0 ) {

				$bp_menu[ $nav_item->parent ][] = $nav_item;

			}

		}

	}
	?>
	<ul>
		<?php
		// Now construct the html.
		foreach ( $bp_menu as $menu => $menu_child ) {

			?>

			<li id="<?php echo esc_attr( $menu ); ?>" class="menu-parent">

				<?php $menu = $transport_menu[ $menu ]; ?>

				<?php $menu_link = ''; ?>

				<?php if ( !empty( $parent_menus[$menu] ) ) { ?>

					<?php $menu_link = bp_core_get_user_domain( get_current_user_id() ) . $parent_menus[$menu]['link']; ?>

				<?php } ?>

				<?php if ( bp_is_user() ) { ?>

					<?php if ( !empty( $parent_menus[$menu] ) ) { ?>

						<?php $menu_link = $parent_menus[$menu]['link']; ?>

					<?php } ?>

				<?php } ?>

				<?php $nicename = bp_members_get_user_nicename( get_current_user_id() ); ?>
				<?php $members_slug = ''; ?>

				<?php $bp_page_option = get_option('bp-pages'); ?>

				<?php if ( !empty( $bp_page_option['members'] ) ) { ?>
					<?php $members_page = get_post(  $bp_page_option['members'], OBJECT ); ?>
					<?php if ( !empty( $members_page) ) { ?>
						<?php $members_slug = $members_page->post_name; ?>
					<?php } ?>
				<?php } ?>

				<?php $menu_link = preg_replace("~".$members_slug."/[^/]+/~",  $members_slug . "/" . $nicename . "/", $menu_link); ?>

				<?php if ( empty( $menu_link ) ) { ?>

					<?php $menu_link = "#"; ?>

				<?php } ?>

				<a href="<?php echo esc_url( $menu_link ); ?>" title="">

					<?php if ( !empty ( $parent_menus[$menu]['name'] ) ) { ?>

						<?php echo thrive_handle_empty_var( $parent_menus[$menu]['name'] ); ?>

					<?php } else { ?>

						<?php echo thrive_handle_empty_var( $menu ); ?>

					<?php } ?>
				</a>

				<?php if ( !empty ( $menu_child ) ) { ?>

				<ul class="sub-menu">

					<?php foreach ( $menu_child as $sub_menu ) { ?>

					<li>

					<?php $sub_menu_link = $sub_menu->link; ?>

					<?php if ( filter_var( $sub_menu_link, FILTER_VALIDATE_URL) === FALSE ) { ?>

    					<?php $sub_menu_link = bp_core_get_user_domain( get_current_user_id() ) . $sub_menu->link; ?>

					<?php } ?>

						<?php $sub_menu_link = preg_replace("~".$members_slug."/[^/]+/~",  $members_slug . "/" . $nicename . "/", $sub_menu_link); ?>

						<a class="<?php echo sanitize_html_class( implode( ' ', $sub_menu->class ) ); ?>" href="<?php echo esc_url( $sub_menu_link ); ?>">

							<?php echo thrive_handle_empty_var( $sub_menu->name ); ?>

						</a>

					</li>

					<?php } ?>

				</ul>

				<?php } ?>


			</li>
			<?php
		}
		?>
		<?php thrive_usernav_logout_link(); ?>
	</ul>

	<?php

		//thrive_pre( $bp_menu );
}

function thrive_bp_navigation() {
    if ( !function_exists('buddypress') ) {
        return;
    }
    $bp_navigation = thrive_bp_get_nav();
    ?>
    <ul>
        <?php foreach ( $bp_navigation as $nav ) { ?>
            <li id="<?php echo esc_attr( $nav['css_id'] ) ?>" class="menu-parent <?php echo esc_attr( $nav['class'] ); ?>">
                <a href="<?php echo esc_url( $nav['link'] ) ?>">
                    <?php echo $nav['name']; ?>
                    <?php echo $nav['count']; ?>
                </a>
                <?php if ( true === $nav['has_subnav'] ) { ?>
                	<?php if ( ! empty( $nav['subnav'] ) ) { ?>
                    <ul class="sub-menu">
                        <?php foreach ( $nav['subnav'] as $subnav ) { ?>
                            <li id="<?php echo esc_attr( $subnav['css_id'] ) ?>" class="<?php echo esc_attr( $subnav['class'] ); ?>">
                                <a href="<?php echo esc_url( $subnav['link'] ) ?>">
                                    <?php echo $subnav['name']; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                <?php } ?>
            </li>
    <?php } ?>
    </ul>
<?php }
function thrive_bp_navigation_old() {
    if ( !function_exists('buddypress') ) {
        return;
    }

    $bp_navigation = thrive_bp_component_nav_setup();
    $current_component = bp_current_component();
    $class = '';
    ?>
    <ul>
        <?php foreach ( $bp_navigation as $menu ) {

            $class = '';

            if ( $current_component == $menu['slug'] ) {
                $class = 'current-menu-item';
            }

            if( true === $menu['primary'] ) { ?>
                    <li id="<?php echo esc_attr( $menu['css_id'] ) ?>" class="menu-parent <?php echo esc_attr( $class ); ?>">
                        <a href="<?php echo esc_url( $menu['link'] ) ?>">
                            <?php echo $menu['component_label']; ?>
                        </a>
                        <ul class="sub-menu">
                            <?php thrive_bp_get_sub_nav( $menu['slug'] ); ?>
                        </ul>
                    </li>
            <?php }
        } ?>
        <?php thrive_usernav_logout_link(); ?>
    </ul>
<?php }
