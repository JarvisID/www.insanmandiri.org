<?php
$url = "https://labs.hahncreativegroup.com/feed/";
$default = '<h3>Visit <a href="http://lplugingarden.com" target="_blank">Plugin Garden - Premium WordPress Plugins</a> for news and info</h3>';

try {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, 'https://labs.hahncreativegroup.com/feed/');
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

	// Execute, grab errors
	$result = curl_exec($ch);
	
	
	if($result !== FALSE) {
		$rss = new SimpleXMLElement($result, LIBXML_NOCDATA);
		if($rss)
		{
			echo '<h3>'.$rss->channel->title.'</h3>';
			$items = $rss->channel->item;
			$count = 0;
			foreach($items as $item)
			{
				$count++;	
				$title = $item->title;
				$link = $item->link;
				$published_on = $item->pubDate;
				$description = $item->description;
				echo '<h4><a href="'.$link.'">'.$title.'</a></h4>';
				echo '<p>'.$description.'</p>';
				if ($count >= 3) {
					break;	
				}
			}
		} 
	} 
	else {
		echo $default;
	}
	
}
catch(Exception $e) {
	echo $default;
}
?>