<?php

function thrive_bp_get_users_messages() {

    if ( !function_exists('buddypress') ) {
        return;
    }

    global $messages_template;
    $messages = '';
    
    if ( ! empty( $messages_template->threads ) ) 
    {
        $messages = $messages_template->threads;
    }

    $user_id = bp_loggedin_user_id();
    $user_message = array();
    $user_messages = array();
    $threads = '';

    $args = array(
        'page'         => 1,
        'per_page'     => 10,
        'page_arg'     => 'mpage',
        'pag_num'     => 0,
        'pag_page'     => 1,
        'box'          => 'inbox',
        'type'         => 'all',
        'user_id'      => $user_id,
        'max'          => false,
        'search_terms' => '',
        'meta_query'   => array(),
    );
    
    if ( 'inbox' === $args['box'] ) {
        $threads = BP_Messages_Thread::get_current_threads_for_user( array(
            'user_id'      => $args['user_id'],
            'box'          => $args['box'],
            'type'         => $args['type'],
            'limit'        => $args['pag_num'],
            'page'         => $args['pag_page'],
            'search_terms' => $args['search_terms'],
            'meta_query'   => $args['meta_query'],
        ) );
    }

    foreach ( $threads['threads'] as $thread ) {
        $user_message['thread_id'] = $thread->thread_id;
        $user_message['messages'] = $thread->messages;
        $user_message['recipients'] = $thread->recipients;
        $user_message['sender_ids'] = $thread->sender_ids;
        $user_message['unread_count'] = $thread->unread_count;
        $user_message['last_message_content'] = $thread->last_message_content;
        $user_message['last_message_date'] = $thread->last_message_date;
        $user_message['last_message_id'] = $thread->last_message_id;
        $user_message['last_message_subject'] = $thread->last_message_subject;
        $user_message['last_sender_id'] = $thread->last_sender_id;
        $user_message['messages_order'] = $thread->messages_order;

        $user_messages[] = $user_message;
    }

    return $user_messages;

}



function thrive_bp_get_message_thread_view_link( $thread_id = 0 ) {
    if ( empty( $thread_id ) ) {
        return;
    }

    /**
     * Filters the thread link base from thread id.
     *
     * @since 2.2.0
     *
     * @param string $value Link to the thread.
     */
    return apply_filters( 'thrive_bp_get_message_thread_view_link', trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() . '/view/' . $thread_id ), $thread_id );
}

function thrive_bp_get_message_thread_excerpt( $last_message_content ) {

    /**
     * Filters the excerpt of the thread message.
     *
     * @since 2.2.0
     *
     * @param string $value Excerpt of the thread message.
     */
    return apply_filters( 'thrive_bp_get_message_thread_excerpt', strip_tags( bp_create_excerpt( $last_message_content, 75 ) ) );
}

function thrive_bp_users_messages() {
    $user_messages = thrive_bp_get_users_messages();
    $thread_link = '';
    $thread_subject = '';
    $thread_sender_id = '';
    $thread_message = '';
    $thread_sender_avatar = '';

    foreach ( $user_messages as $message ) {
        $thread_link = thrive_bp_get_message_thread_view_link( $message['thread_id'] );
        $thread_subject = $message['last_message_subject'];
        $thread_sender_id = $message['last_sender_id'];
        $args = array(
            'item_id' => $thread_sender_id,
            'object' => 'user',
            'type' => 'thumb'
        );
        $thread_sender_avatar = bp_core_fetch_avatar( $args );
        $thread_message = thrive_bp_get_message_thread_excerpt( $message['last_message_content'] );
        ?>
        <li>
    		<a class="message-item-link" href="<?php echo esc_url( $thread_link ); ?>" title="<?php echo sprintf( __( '%s &raquo; Read message ', 'thrive' ), $thread_subject ); ?>">
    			<div class="row">
    				<div class="col-xs-2 messages-avatar"><?php echo $thread_sender_avatar; ?></div>
    				<div class="col-xs-10 messages-details">
    					<h5><?php echo esc_html( $thread_subject ); ?></h5>
    					<p><?php echo esc_html( $thread_message ); ?></p>
    				</div>
    			</div>
    		</a>
    	</li>

    <?php }
}

function display() {
    global $messages_template;
    $messages = $messages_template->threads;

    // thrive_bp_users_messages();
    // var_dump( $messages );
}
// add_action( 'the_content', 'display' );
