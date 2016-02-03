<?php

//This function should be placed in the functions.php file in the theme directory under wp-content

/** KJHK Ads shortcode
*	@PARAMS:  $atts is because wordpress is picky, $content should be the name of a subdirectory
*          in the kjhk.org/web/kjhk-ads/ directory
*          NOTE: the directory should not include '/' at the beginning or end, just the folder name
*	@PRE:     If the content_data.json (replace content with the ad set name) file for the ad set exists, it it properly formatted
*	@POST:    If no content_data.json file for the ad set exists to store impressions, this creates one
*          If the content_data file already exists, this updates the impression count
*	@RETURNS: 'error: no such path as $path' if the directory doesn't exist, or picks a random file
*          from within the specified directory and returns its contents
*/

function kjhkAdsEmbed($content = null)
{
	//This is the directory that the function will search for ads
	$path = '/var/www/kjhk.org/web/kjhk-ads/' .$content . '/';

	//Make sure the requested ad set exists, exit and return error message if it doesn't
	if(!is_dir($path))
	{
		return 'error: no such path as ' . $path;
	}

	//Scan the target directory
	$results = scandir($path);

	//This will store the html code of the ads, used to give one back at random
	$myAds = array();

	//This will store the results without non-ad files
	$clean_results = array();

	//Populate myAds with the contents of all desired files in desired directory
	foreach ($results as $result)
	{
		//This helps us ignore anything that is not an html file
		$file_parts = pathinfo($path . $result);
		//Ignore undesired files
		if ($result === '.' or $result === '..' or $file_parts['extension'] != 'html') continue;

		//Add the file to myAds and add the current result to clean_results
		if (is_file($path . $result)) {
			$myAds[] = file_get_contents($path . '/' . $result);
			$clean_results[] = $result;
		}
	}

	$rand_num = mt_rand(0, count($myAds) - 1);


	//This handles storing of impression counts
	if(!is_file($path . $content . '_data.json'))
	{
		//DATA FILE DOES NOT ALREADY EXIST
		//create the data array and set all impressions to 0
		$data = array();
		foreach($clean_results as $result)
		{
			$data_entry = array();
            $data_entry['impressions'] = 0;
			$data[$result] = $data_entry;
		}

		//add 1 to the impressions of the data_entry of the ad to be displayed
		$data[$clean_results[$rand_num]]['impressions']+=1;
		//store the data in a json file
		file_put_contents($path . $content . '_data.json', json_encode($data));
	}
	else
	{
		//DATA FILE ALREADY EXISTS
        //get the previous data
		$data = json_decode(file_get_contents($path . $content . '_data.json'), true);

		//checks if the picked ad is new, adds it to data with 1 impression if it is
		if(!array_key_exists($clean_results[$rand_num],  $data))
		{
            $data_entry = array();
            $data_entry['impressions'] = 1;
			$data[$clean_results[$rand_num]] = $data_entry;
		}
		else
        {
			//increments the appropriate data entry impressions if the ad is not new
			$data[$clean_results[$rand_num]]['impressions']+=1;
		}
		//store the data in a json file
		file_put_contents($path . $content . '_data.json', json_encode($data));
	}

	return $myAds[$rand_num];
}

echo kjhkAdsEmbed('animals');

//Uncomment this when adding to functions.php
//add_shortcode( 'kjhk', 'kjhk_func' );

?>