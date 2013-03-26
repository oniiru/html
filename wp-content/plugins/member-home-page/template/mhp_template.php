<?php
/**
 * Contains base class for templates
 * @author Anton Matiyenko (amatiyenko@gmail.com)
 */

/**
 * Template base class => used mostly to built parts of input HTML
 */
class MHP_Template {
	/**
	 * Draws options for a select
	 * @param string $value
	 * @param array $options
	 * @param string $mode defines what to use to match value: either keys or values of array items
	 * @return string
	 */
	protected function options($value, $options, $mode = 'values'){
		$output = '';
		foreach($options as $k => $option) {
			if($mode=='keys') {
				$output .= '<option value="' . $k .'"' . (($k==$value)?' selected="selected"':'') . '>' . $option . '</option>';
			} else {
				$output .= '<option value="' . $option .'"' . (($option==$value)?' selected="selected"':'') . '>' . $option . '</option>';
			}
		}
		return $output;
	}
	
	/**
	 * Showes error message if it's present for the current field
	 * @param string $field
	 * @param array $data
	 * @return string
	 */
	protected function errorMessage($field, $data){
		$output = '';
		if(isset($data['errors'])) {
			if(isset($data['errors'][$field])) {
				$output = '<span class="mhp_error" style="color: #FF0000;">' . __($data['errors'][$field], 'member_home_page') . '</span>';
			}
		}
		return $output;
	}
}
?>
