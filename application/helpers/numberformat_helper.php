<?php

function numberformat($price)
{
	$result = number_format($price,2,",",".");
	
	return $result;
}	