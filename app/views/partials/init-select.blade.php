<?php
	$options = array();
	foreach($states as $state){
		$options[$state] = $state;
	}
?>
{{ Form::select('init_state_name', $options, Input::old('init_state_name'), array('class' => 'form-control', 'id' => 'init_state_name') ) }}