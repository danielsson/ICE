<?php
namespace Ice;

/**
 * Tool for searching directories for files filtered
 * by their contents.
 */
class FileScanner {
	public $mask = "";

	public $pathlist = array();

	public function __construct($m) {
		$this->mask = $m;
	}
	
	/**
	 * Recursively search $dir for filenames matching mask.
	 * 
	 * @param string $dir The directory to search
	 * @return string[] paths
	 */
	public function get_file_paths($dir) {
		$files = scandir($dir);
		$path = "";
		foreach($files as $file) {
			if ($file == '.' 
				or $file == '..'
				or $file[0] == '.'
				or file_exists($dir . '/ice-adapter.php') //Ensure that we dont index the sys folder
				or preg_match($this->mask, $file) == 0) {

				continue;
			}

			$path = $dir . DIRECTORY_SEPARATOR . $file;
			if(is_dir($path)){
				$this->get_file_paths($path);
			} else {
				$this->pathlist[] = $path;
			}
		}
		return $this->pathlist;

	}
	public function make_paths_relative_to_doc_root() {
		$i = 0;
		$arrlen = count($this->pathlist);
		$rootlen = strlen($_SERVER['DOCUMENT_ROOT']);
		for(;$i < $arrlen; $i++) {
			$this->pathlist[$i] = substr($this->pathlist[$i], $rootlen);
		}
		return $this->pathlist;
	}
	
	/**
	 * Filters the list of found files based on a regex.
	 * 
	 * Search all the files found with get_file_paths for
	 * $regex. $scan_lines number of lines will be scanned.
	 * 
	 * @param RegExp $regex The regexp to match
	 * @param int $scan_lines The number of lines to scan. 0 scans all lines.
	 */
	public function filter_files($regex, $scan_lines) {
		$line = "";
		$numlines = 0;
		$out = array();
		foreach($this->pathlist as $path) {
			if(file_exists($path) && is_readable($path)) {
				$file = @fopen($path, 'r');
				if($file) {
					$numlines = 0;
					while(($line = fgets($file, 4096)) !== false) {
						if(preg_match($regex, $line) === 1) {
							$out[] = $path;
							break;
						}
						$numlines++;
						if($scan_lines > 0 and $numlines > $scan_lines) break;
					}
				}
				fclose($file);
			}
		}
		$this->pathlist = $out;
		return $this->pathlist;
	}
}?>