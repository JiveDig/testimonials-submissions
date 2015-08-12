# Testimonials - Submissions
Front end posting of 'Testimonials by WooThemes' plugin. Form and processing via CMB2 (CMB2 is built in to this plugin)

**This is a 3rd party plugin, no affiliation with WooThemes.**

## Shortcode
`[tbws_form]`

`[tbws_form post_status="%your status here%"]`

Use shortcode `[tbws_form]` to insert the submission form on any WP post/page.
Submissions default to a post status of **draft** so they can be manually approved.
If you want your submissions to be published immediately you can use the `post_status` attribute in the shortcode. Example `[tbws_form post_status="publish"]`.

## Hooks & Filters
There are a number of hooks and filters included in the plugin for quickly customizing the form

### * Form output
`tbws_submission_form`

This filter runs at prior to outputting the form via the shortcode. You can use it to display content, or restrict access to the form for various conditions.

Example usage:
```
// Restric access to testimonial submission for if not logged in
add_filter( 'tbws_submission_form', 'prefix_form_require_logged_in_user' );
function prefix_form_require_logged_in_user( $output ) {
    // Show a login form if not logged in
    if ( ! is_user_logged_in() ) {
        $output = '<p>' . __( 'You must be a logged in to submit a testimonial.', 'text-domain' ) . '</p>';
        $output .= wp_login_form( array( 'echo' => false ) );
        return $output;
    }
    // Otherwise, show the default output
    return $output;
}
```

### * Form fields
**These hook allows you to add more fields to the form.**
Accepts anything CMB2 allows, including any custom field types you create

`tbws_form_before`

`tbws_form_content_field_before`

`tbws_form_after`

Example usage:
```
// Add a new text field after the form
add_action( 'tbws_form_after', 'prefix_my_new_form_field' );
function prefix_my_new_form_field( $cmb ) {
    $cmb->add_field( array(
        'name'    => __( 'New Field Title', 'tbws' ),
        'desc'    => __( 'New field description (optional)', 'tbws' ),
        'id'      => 'submitted_post_before', // ID can be whatever you want, use this for processing later
        'type'    => 'text_url',
    ) );
}
```

### * Form data processing
**These filters allow you to process the data submitted by the form, including any custom form fields you may have added**

`tbws_submitted_post_data` - Filter the post data prior to wp_insert_post

`tbws_submitted_post_meta` - Filter the post meta prior to update_post_meta

`tbws_content_kses` - Change wp_kses arguments
See https://codex.wordpress.org/Function_Reference/wp_kses

### * Form redirect
** Filter the redirect url**

`tbws_success_redirect`

### * Form success message
** Filter the success message HTML

`tbws_success_message`
