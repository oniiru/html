<?php 
 /* @param string $meta Meta name.	 
 * @param array $details Contains the details for the field.	 
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */


$element .= '<input type="text" name="'. esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'] ) ) .'" id="'. $frontend_prefix . esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'] ) ) .'" value="'. esc_attr( $value ) .'" class="mb-text-input mb-field"/>';
?>