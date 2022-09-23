<?php

function load_type_field($options_name,$options_value,$options_type)
{
	if($options_type == 'text')
	{
		$field = '<div class="control-group">
						<label class="control-label" for="'.$options_name.'">'.$options_name.'</label>
						<div class="controls">
						<input type="text" class="span8" id="'.$options_name.'" placeholder="'.$options_name.'" name="'.$options_name.'" value="'.$options_value.'">
						</div>
					</div>';
	}
	else 
	if($options_type == 'textarea')
	{
		$field = '<div class="control-group">
						<label class="control-label" for="'.$options_name.'">'.$options_name.'</label>
						<div class="controls">
						<textarea class="span8" rows="5" id="'.$options_name.'" placeholder="'.$options_name.'" name="'.$options_name.'" >'.$options_value.'</textarea>
						</div>
					</div>';
	}
	else
	{
		$field = '<div class="control-group">
						<label class="control-label" for="'.$options_name.'">'.$options_name.'</label>
						<div class="controls">
						<input class="span8" type="text" id="'.$options_name.'" placeholder="'.$options_name.'" name="'.$options_name.'" value="'.$options_value.'">
						</div>
					</div>';
	}

	return $field;
}
	