<?php
 /* @param string $meta Meta name.	 
 * @param array $details Contains the details for the field.	 
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */
 
$element .= '<select name="'. esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'] ) ) .'"  id="'. $frontend_prefix . esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'] ) ) .'" class="mb-select mb-field" >';
			
if( !empty( $details['default-option'] ) && $details['default-option'] )
	$element .= '<option value="">'. __('...Chose', 'wck') .'</option>';

if( !empty( $details['options'] ) ){

		$i = 0;
		$options = '';
		foreach( $details['options'] as $option ){
			
			if( strpos( $option, '%' ) === false ){
				$label = $option;
				if( !empty( $details['values'] ) )
					$value_attr = $details['values'][$i];
				else 
					$value_attr = $option;
			}
			else{
				$option_parts = explode( '%', $option );
				if( !empty( $option_parts ) ){
					if( empty( $option_parts[0] ) && count( $option_parts ) == 3 ){
						$label = $option_parts[1];
						if( !empty( $details['values'] ) )
							$value_attr = $details['values'][$i];
						else
							$value_attr = $option_parts[2];
					}
				}
			}
				
			$options .= '<option value="'. esc_attr( $value_attr ) .'"  '. selected( $value_attr, $value, false ) .' >'. esc_html( $label ) .'</option>';
			$i++;
		}
}				
$field_name = Wordpress_Creation_Kit::wck_generate_slug( $details['title'] );
$element .= apply_filters( "wck_select_{$meta}_{$field_name}_options", $options );	
$element .= '</select>';
?>