<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @link http://webdevstudios.com/2015/03/30/use-cmb2-to-create-a-new-post-submission-form/
 * @link https://github.com/WebDevStudios/CMB2-Snippet-Library/blob/master/front-end/cmb2-front-end-submit.php
 * Original tutorial & code
 */


/**
 * Register the form and fields for our front-end submission form
 */
add_action( 'cmb2_init', 'tbws_testimonial_form_register' );
function tbws_testimonial_form_register() {

    /**
     * Create the metabox
     * don't save any values - front end only
     */
    $cmb = new_cmb2_box( array(
        'id'           => 'tbws_testimonial',
        'object_types' => array( 'testimonial' ),
        'hookup'       => false,
        'save_fields'  => false,
        'cmb_styles'   => false,
    ) );


    $cmb->add_field( array(
        'name'    => __( 'Name', 'tbws' ),
        'id'      => 'submitted_post_title',
        'type'    => 'text',
    ) );
    $cmb->add_field( array(
        'name'    => __( 'Email', 'tbws' ),
        'id'      => 'submitted_post_email',
        'type'    => 'text_email',
    ) );
    $cmb->add_field( array(
        'name'    => __( 'Byline', 'tbws' ),
        'desc'    => __( 'Your business name, position, or title', 'tbws' ),
        'id'      => 'submitted_post_byline',
        'type'    => 'text',
    ) );
    $cmb->add_field( array(
        'name'    => __( 'Website', 'tbws' ),
        'desc'    => __( 'Your website', 'tbws' ),
        'id'      => 'submitted_post_url',
        'type'    => 'text_url',
    ) );

    // Hook for developers to add new fields before content field
    do_action( 'tbws_content_field_before', $cmb );

    $cmb->add_field( array(
        'name'    => __( 'Testimonial', 'tbws' ),
        'id'      => 'submitted_post_content',
        'type'    => 'textarea',
    ) );

    // Hook for developers to add new fields after content field
    do_action( 'tbws_content_field_after', $cmb );

}


/**
 * Gets the offer-new cmb instance
 *
 * @return CMB2 object
 */
function tbws_testimonial_cmb2_get() {
    // Use ID of metabox in wds_frontend_form_register
    $metabox_id = 'tbws_testimonial';
    // Post/object ID is not applicable since we're using this form for submission
    $object_id  = 'tbws_testimonial_id';
    // Get CMB2 metabox object
    return cmb2_get_metabox( $metabox_id, $object_id );
}


/**
 * Handle the new_offer shortcode
 *
 * @param  array
 * @return string       Form html
 */
add_shortcode( 'testimonial_form', 'tbws_do_new_offer_submission_shortcode' );
function tbws_do_new_offer_submission_shortcode() {

    // Hook for developers to check if user can access the form
    do_action( 'tbws_submission_access' );

    // Initiate our output variable
    $output = '';

    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        $output .= '<div id="message" class="success">' . __( 'Thank you! Your testimonial has been submitted.', 'tbws' ) . '</div>';
        return apply_filters( 'tbws_success_message', $output );
    }

    // Get CMB2 metabox object
    $cmb = tbws_testimonial_cmb2_get();

    // Parse attributes
    $atts = shortcode_atts( array(
        'post_status' => 'draft', // Set status to draft by default
    ), $atts, 'tbws_testimonial' );

    /*
     * Let's add these attributes as hidden fields to our cmb form
     * so that they will be passed through to our form submission
     */
    foreach ( $atts as $key => $value ) {
        $cmb->add_hidden_field( array(
            'field_args'  => array(
                'id'      => 'submitted_' . $key,
                'type'    => 'hidden',
                'default' => $value,
            ),
        ) );
    }

    $form_args = array(
        'save_button' => __( 'Submit Testimonial', 'tbws' ),
    );

    // Get our form
    $output .= cmb2_get_metabox_form( $cmb, 'tbws_testimonial_id', $form_args );

    return $output;

}


/**
 * Handles form submission on save
 *
 * @param  CMB2  $cmb       The CMB2 object
 * @param  array $post_data Array of post-data for new post
 * @return mixed            New post ID if successful
 */
add_action( 'cmb2_after_init', 'tbws_process_offer_submission' );
function tbws_process_offer_submission( $cmb, $post_data = array(), $post_meta = array() ) {

    // If no form submission, bail
    if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
        return false;
    }

    // Bail if not the submission object we need
    if ( $_POST['object_id'] != 'tbws_testimonial_id' ) {
        return false;
    }

    // Get CMB2 metabox object
    $cmb = tbws_testimonial_cmb2_get();

    // Check security nonce
    if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
        return $cmb->prop( 'submission_error', new WP_Error( 'security_fail', __( 'Security check failed. Please try again.', 'tbws' ) ) );
    }

    // Set our default post_type to post
    $post_data['post_type'] = 'testimonial';

    /**
     * Fetch sanitized values
     */
    $sanitized_values = $cmb->get_sanitized_values( $_POST );

    // Set our post title ( Name )
    if ( ! empty( $_POST['submitted_post_title'] ) ) {
        $post_data['post_title'] = wp_strip_all_tags( $sanitized_values['submitted_post_title'] );
        unset( $sanitized_values['submitted_post_title'] );
    } elseif ( empty( $_POST['submitted_post_title'] ) ) {
        return $cmb->prop( 'submission_error', new WP_Error( 'name_missing', __( 'Please enter your name.', 'tbws' ) ) );
    }

    // Set post status
    $post_data['post_status'] = $sanitized_values['submitted_post_status'];

    /**
     * Setup allowed HTML for textarea
     * @link https://codex.wordpress.org/Function_Reference/wp_kses
     */
    $allowed_html = array(
        'a' => array(
            'href'  => array(),
            'title' => array()
        ),
        'em'     => array(),
        'strong' => array(),
    );

    // Add filter for developers to change wp_kses arguments
    // See https://codex.wordpress.org/Function_Reference/wp_kses
    $allowed_html = apply_filters( 'tbws_content_kses', $allowed_html );

    // Set our post content
    if ( ! empty( $_POST['submitted_post_content'] ) ) {
        $post_data['post_content'] = wp_kses( $sanitized_values['submitted_post_content'], $allowed_html );
        unset( $sanitized_values['submitted_post_content'] );
    } elseif ( empty( $_POST['submitted_post_content'] ) ) {
        return $cmb->prop( 'submission_error', new WP_Error( 'testimonial_missing', __( 'Please enter a testimonial.', 'tbws' ) ) );
    }

    // Filter the post data prior to inserting post
    $post_data = apply_filters( 'tbws_submitted_post_data', $post_data );

    // Create the new post
    $new_submission_id = wp_insert_post( $post_data, true );

    // If we hit a snag, update the user
    if ( is_wp_error( $new_submission_id ) ) {
        return $cmb->prop( 'submission_error', $new_submission_id );
    }

    // Set the email
    if ( ! empty( $_POST['submitted_post_email'] ) ) {
        $post_meta['_gravatar_email'] = $sanitized_values['submitted_post_email'];
        unset( $sanitized_values['submitted_post_email'] );
    }

    // Set the byline
    if ( ! empty( $_POST['submitted_post_byline'] ) ) {
        $post_meta['_byline'] = $sanitized_values['submitted_post_byline'];
        unset( $sanitized_values['submitted_post_byline'] );
    }

    // Set the byline
    if ( ! empty( $_POST['submitted_post_url'] ) ) {
        $post_meta['_url'] = $sanitized_values['submitted_post_url'];
        unset( $sanitized_values['submitted_post_url'] );
    }

    // Filter the post meta prior to inserting post
    $post_meta = apply_filters( 'tbws_submitted_post_meta', $post_meta );

    // Get our key and value from $post_meta array
    foreach( $post_meta as $key => $value ) {
        update_post_meta( $new_submission_id, $key, $value );
    }

    // Redirect to new post with query string
    $redirect = esc_url_raw( add_query_arg( 'success', 'true', get_permalink() ) );
    // Add filter for developers to change redirect url
    $redirect = apply_filters( 'tbws_success_redirect', $redirect );

    /**
     * Redirect after form submission
     * This will help double-submissions with browser refreshes
     */
    wp_redirect( $redirect );
    exit;

}
