<?php 
function seo_url($result) { //fungsi ubah spasi %20 dan plus jadi minus pada permalink search
$result = strtolower($result);
	$result = preg_replace('/&.+?;/', '', $result);
	$result = preg_replace('/\s+/', '-', $result);
        $result = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '-', $result);
	$result = preg_replace('|-+|', '-', $result);
        $result = preg_replace('/&#?[a-z0-9]+;/i','',$result);
        $result = preg_replace('/[^%A-Za-z0-9 _-]/', '-', $result);
	$result = trim($result, '-');
	return $result;
}
?>