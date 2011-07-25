<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package	   Fuel
 * @version	   1.0
 * @author	   Fuel Development Team
 * @license	   MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link	   http://fuelphp.com
 */
namespace Fuel\Tasks;

/**
 * Task that will Generate Translated (via Bing's translate API) lang files
 *
 * @author jondavidjohn
 */
class Translate {
	
	// Bing APP ID
	const APP_ID  = "D323329183BEA777086DACBEE261B1F3035F406F";
	const REQ_URL = "http://api.microsofttranslator.com/v2/Http.svc/Translate";

	public static function run($to, $from = "en", $dir_path = APPPATH.'lang')
	{
		
		$translated_file_array = array();
		$file_array = array();
		
		$lang_dir = opendir($dir_path);
		while($file = readdir($lang_dir))
		{
			//make sure file is not a directory, parent, index.html, or .gitkeep
			$ignore = array(
				'.',
				'..',
				'.gitkeep',
				'index.html'
			);
			
			if ( !is_dir($lang_dir.$file) && !in_array($file, $ignore) )
			{
				$file_array[$file] = eval(file_get_contents($lang_dir.$file));
			}
			
			foreach($file_array as $file => $lang_array)
			{
				foreach($lang_array as $label => $lang)
				{
					$translated_file_array[$file][$label] = self::translateLang($lang,$from,$to);
				}
			}
			
			mkdir($dir_path.$to.'/');
			
			foreach($translated_file_array as $file = $data)
			{
				$php_data = \Format::to_php($data);
				$new_file = <<<FILE
<?php
/***
 * Lang file generated by fuel-translate - http://url/to/github
 */
return $data

// End of File : $file
FILE;

				file_put_contents($dir_path.$to.'/'.$file);
			}
		}
	}
	
	private static function translateLang($lang,$from,$to)
	{		
		if (is_array($lang))
		{
			foreach($lang as $element)
			{
				self::translateLang($element,$from,$to);
			}
		}
		else
		{
			$reqUrl = REQ_URL."appId=".APP_ID."&from={$from}&to={$to}".$lang;
			return file_get_contents($reqUrl);
		}
	}
	
	public static function singlefile($path)
	{
		
	}
}

/* End of file translate.php */