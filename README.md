# Testimonials - Submissions
Front end posting of 'Testimonials by WooThemes' plugin. Form and processing via CMB2 (CMB2 is built in to this plugin)

**This is a 3rd party plugin, no affiliation with WooThemes.**

## Shortcode
```
[tbws_form]
```
```
[tbws_form post_status="%your status here%"]
```
Use shortcode `[tbws_form]` to insert the submission form on any WP post/page.
Submissions default to a post status of **draft** so they can be manually approved.
If you want your submissions to be published immediately you can use the `post_status` attribute in the shortcode. Example `[tbws_form post_status="publish"]`.

## Hooks & Filters
There are a number of hooks and filters included in the plugin for quickly customizing the form

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
