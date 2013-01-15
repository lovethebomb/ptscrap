<?php
require 'simplehtmldom_1_5/simple_html_dom.php';


class ptscrap extends simple_html_dom {
	private $url;
	private $user;
	private $board;
	private $html;
	private $images;
	private $pins;
	private $file;

	/**
	 * Main constructor
	 * @param String $url URL String
	 */
	public function __construct($url) {
		$url = "http://www.pinterest.com" . $url;
		$pinsPerPage = 50;
		$urlArray = explode('/', $url);
		$this->user = $urlArray[3];
		$this->board = $urlArray[4];
		$this->url = $url;
		$this->html = file_get_html($url);

		if($this->html) {
		    foreach($this->html->find('div[id=BoardStats]') as $pin) {
		        $plaintext = $pin->innertext;
		    }
		    
		    if(strstr($plaintext, ',')) {
			    $plaintext = explode(',', $plaintext);
			    $plaintext = explode('>', $plaintext[1]);
			    $pins = (int)$plaintext[1];
			    $this->pins = $pins;	
		    } else {
		    	$plaintext = explode('>', $plaintext);
		    	$plaintext = explode('<', $plaintext[1]);
			    $pins = (int)$plaintext[0];
			    $this->pins = $pins;	
		    }

		    if($pins != 0) {
			    if($pins > 50)
			        $pages = ceil($pins/$pinsPerPage);
			    else
			        $pages = 1;

			    $this->pages = $pages; 
			    $this->html->clear();
			} else {
				return false;
			}
			
		// If no data scraped, return false	
	    }  else {
	    	return false;
	    }
	}

	/**
	 * Scrap Pinterest page
	 * @return Mixed false or Array of elements
	 */
	public function scrape() {
		if($this->html) {
			$array = Array();
		    // get article block
		    for($i = 1; $i <= $this->pages; $i++) {
		    	$this->html = file_get_html($this->url . "?page=" . $i);
			    foreach($this->html->find('div[class=pin]') as $article) {
			        $array[] = $article->attr['data-closeup-url'];
			    }
		    }
		    
		    // clean up memory
		    $this->html->clear();
		    unset($this->html);
		    $this->images = $array;
		    return $array;
		}
		else {
			return false;
		}
	}

	/**
	 * Create the connection with cURL to URL
	 * @return Boolean 
	 */
	public function create() {
    	$i = 1;
	    foreach($this->images as $page) {
            $ch = curl_init ($page);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $raw=curl_exec($ch);
            curl_close ($ch);
            $saveto = "tmp/pinterest_".$this->user. "_" . $this->board . "_" . $i . ".jpg";
            
            if(file_exists($saveto)){
                unlink($saveto);
            }

            $fp = fopen($saveto,'x');
            fwrite($fp, $raw);
            fclose($fp);
            $i++;
	    }
	    return true;
	}

	/**
	 * Deliver final zip to download
	 * @return Boolean 
	 */
	public function deliver() {
		 // we deliver a zip file
		// filename for the browser to save the zip file
		$tmp = tempnam("tmp", "tempname");
		$tmp_zip = $tmp . ".zip";
		$path = "pinterest_" . $this->user . "_" . $this->board .   ".zip";
		chdir("tmp");
		unlink($tmp);	

		exec('zip -o ' . $path . ' ' . $tmp_zip . ' * -x index.html');
		$filesize = filesize($path);

		exec('mv ' . $path . ' ../files');
		$this->file = $path;

		$files = glob("*.jpg");
		foreach($files as $file) {
			unlink($file);
		}

		return true;
	}

	/**
	 * returns URL of Object
	 * @return String URL
	 */
	public function getUrl() {
		return $this->url;
	}	
	
	/**
	 * returns Pages number
	 * @return Integer Number of pages to scrap
	 */
	public function getPages() {
		return $this->pages;
	}	

	/**
	 * returns number of Pins of current board
	 * @return Integer Number of Pins
	 */
	public function getPins() {
		return $this->pins;
	}	

	/**
	 * returns zip filename
	 * @return String Filename to download
	 */
	public function getFilename() {
		return $this->file;
	}	
}

?>