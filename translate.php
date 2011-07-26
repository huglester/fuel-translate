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
	const LANGS = "ar,bg,ca,zh,cs,da,nl,en,et,fi,fr,de,el,ht,he,hu,id,it,ja,ko,lv,lt,no,po,pt,ro,ru,sk,sl,es,sv,th,tr,uk,vi";
	const APP_ID  = "D323329183BEA777086DACBEE261B1F3035F406F";
	const REQ_URL = "http://api.bing.net/json.aspx?";

	public static function run($to, $from = "en", $lang_path = false)
	{
		
		$available_langs = explode(',',self::LANGS);
		
		if (!in_array($to, $available_langs))
		{
			\Cli::error("Language code '$to' not available");
			\Cli::error("Exiting...");
			exit();
		}
		
		if (!$lang_path) {
			$lang_path = APPPATH.'lang';
		}
		else if (!is_dir($lang_path))
		{
			\Cli::error("Root Lang path not found at: $lang_path");
			\Cli::error("Exiting...");
			exit();
		}

		$translated_file_array = array();
		$file_array = array();
		
		if (!is_dir("$lang_path/$from"))
		{
			\Cli::error("Original langs not found at: $lang_path/$from");
			\Cli::error("Exiting...");
			exit();
		}

		$lang_dir = opendir($lang_path.'/'.$from);
		while(($file = readdir($lang_dir)) !== false )
		{
			//make sure file is not a directory, parent, index.html, or .gitkeep
			$ignore = array(
				'.',
				'..',
				'.gitkeep',
				'index.html'
			);
			if ( !is_dir($lang_path.'/'.$from.'/'.$file) && !in_array($file, $ignore) )
			{
				\Cli::write("Translating lang file - $from/$file");
				$file_array[$file] = include $lang_path.'/'.$from.'/'.$file;						
			}
		}

		foreach($file_array as $file => $lang_array)
		{

			foreach($lang_array as $label => $lang)
			{
				$translated_file_array[$file][$label] = self::translateLang($lang,$from,$to);
			}
		}

		self::mkdir_recursive($lang_path.'/'.$to.'/', 0755);

		foreach($translated_file_array as $file => $data)
		{
			$format = \Format::factory($data);
			$php_data = $format->to_php($data);
			$new_file = <<<FILE
<?php
/***
* Lang file generated by fuel-translate - https://github.com/jondavidjohn/fuel-translate
*/
return $php_data;

/* End of file $file */
FILE;

			if(file_put_contents($lang_path.'/'.$to.'/'.$file, $new_file) === false)
			{
				/*
					TODO ERROR
				*/
			}
			else
			{
				\Cli::write("Translation Successful - $to/$file", "green");	
			}
		}
	}

	private static function translateLang($lang,$from,$to)
	{		
		if (is_array($lang))
		{
			$tmp_array = array();
			foreach($lang as $label => $element)
			{
				$tmp_array[$label] = self::translateLang($element,$from,$to);
			}
			return $tmp_array;
		}
		else
		{
			//extract placeholders
			preg_match_all("/:\w+/", $lang, $placeholders);
			$placeholders = $placeholders[0];
			
			//replace with meaningless placeholders
			foreach($placeholders as $i=>$p)
			{
				$lang = preg_replace("/{$p}/", '|'.$i.'|', $lang, 1 );
			}
			
			//translate
			$reqUrl = self::REQ_URL

	            // Common request fields (required)
	            . "AppId=" . self::APP_ID
	            . "&Query=" . urlencode($lang)
	            . "&Sources=Translation"

	            // Common request fields (optional)
	            . "&Version=2.2"

	           // SourceType-specific fields (required)
	            . "&Translation.SourceLanguage={$from}"
	            . "&Translation.TargetLanguage={$to}";

			$response = json_decode(file_get_contents($reqUrl));
			$translated_lang = $response->SearchResponse->Translation->Results[0]->TranslatedTerm;
			
			//re-add placeholders (untranslated)
			foreach($placeholders as $i=>$p)
			{
				$translated_lang = preg_replace("/\|{$i}\|/", $p, $translated_lang, 1);
			}
			
			return $translated_lang;
		}
	}

	/***
	 * http://www.php.net/manual/en/function.mkdir.php#81656
	 */
	private static function mkdir_recursive($pathname, $mode)
	{
    	is_dir(dirname($pathname)) || self::mkdir_recursive(dirname($pathname), $mode);
    	return is_dir($pathname) || @mkdir($pathname, $mode);
	}
}

/* End of file translate.php */