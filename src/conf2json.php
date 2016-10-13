<?php
/**
 * conf2json
 *
 * Library that converts PHP configuration files to JSON.
 *
 * @author Víctor Díaz <victor@axiomer.com>
 * @website https://victordiaz.me
 * @copyright 2016 Víctor Díaz
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0.0
 */

namespace victordzmr;

use Exception;

/**
 * conf2json class.
 */
class conf2json {
	
	/**
	 * File or directory that contains the files to be converted.
	 *
	 * @var string
	 * @access private
	 */
	private $input;
	
	/**
	 * Directory where the output files will be saved.
	 *
	 * @var string
	 * @access private
	 */
	private $output;
	
	/**
	 * Whether the output files must be encoded using the JSON_PRETTY_PRINT option.
	 *
	 * @var bool
	 * @access private
	 */
	private $pretty;
	
	/**
	 * Whether the conversion must be recursive when the input is a directory.
	 *
	 * @var bool
	 * @access private
	 */
	private $recursive;
	
	/**
	 * Whether the library should print information to standard and error outputs.
	 *
	 * @var bool
	 * @access private
	 */
	private $verbose;
	
	/**
	 * Initialize a conf2json object.
	 *
	 * @access public
	 * @param ?string $input File or directory that contains the files to be converted (default: __DIR__).
	 * @param ?string $output Directory where the output files will be saved (default: __DIR__).
	 * @param ?bool $pretty Whether the output files must be encoded using the JSON_PRETTY_PRINT option (default: true).
	 * @param ?bool $recursive Whether the conversion must be recursive when the input is a directory (default: true).
	 * @param ?bool $verbose Whether the library should print information to standard and error outputs (default: false).
	 * @throws Exception
	 */
	public function __construct($input = null, $output = null, $pretty = null, $recursive = null, $verbose = null) {
		
		// Set attributes
		$this->input = $input ?? __DIR__;
		$this->output = $output ?? __DIR__;
		$this->pretty = $pretty ?? true;
		$this->recursive = $recursive ?? true;
		$this->verbose = $verbose ?? false;
		
		// Check input and output directories
		if (!file_exists($this->input))
			throw new Exception('The input file or directory does not exist or it is inaccessible.');
		
		if (!file_exists($this->output))
			mkdir($this->output, 0755, true);
		
		// Normalize attributes
		$this->input = realpath($this->input);
		$this->output = realpath($this->output);
		$this->pretty = filter_var($this->pretty, FILTER_VALIDATE_BOOLEAN);
		$this->recursive = filter_var($this->recursive, FILTER_VALIDATE_BOOLEAN);
		$this->verbose = filter_var($this->verbose, FILTER_VALIDATE_BOOLEAN);
	}
	
	/**
	 * Perform a conversion.
	 *
	 * @access public
	 */
	public function run() {
		
		// Save start time
		if ($this->verbose)
			$start_time = microtime(true);
		
		// Check if the input is a file or a directory
		$is_file = is_file($this->input);
		
		// Get file paths
		if ($is_file)
			$files = [$this->input];
		else
			$files = glob($this->input . ($this->recursive ? DIRECTORY_SEPARATOR . '**' : '') . DIRECTORY_SEPARATOR . '*.php');
		
		// Check if there are files to be converted
		$total = count($files);
		
		if ($total === 0) {
			if ($this->verbose)
				error_log('No files to be converted.');
			
			exit;
		}
		
		// Convert them
		foreach ($files as $i => $file) {
			
			// Destination filename and path
			$destination_filename = basename($file, '.php') . '.json';
			
			if ($is_file) {
				$destination_single = $destination_filename;
				$destination_path = $this->output;
			} else {
				$destination_single = str_replace($this->input, '', $file);
				$destination_path = dirname($this->output . $destination_single);
				
				$destination_single = substr($destination_single, 1);
			}
			
			// Create destination path if appropriate
			if (!file_exists($destination_path))
				mkdir($destination_path, 0755, true);
			
			// Retrieve the file
			$conf = require $file;
			
			// Convert the file to JSON
			$conf = json_encode($conf, ($this->pretty ? JSON_PRETTY_PRINT : 0));
			
			// Correct indentation
			if ($this->pretty)
				$conf = preg_replace('/^    |\G    /m', "\t", $conf);
			
			// Save the file
			file_put_contents(
				$destination_path . DIRECTORY_SEPARATOR . $destination_filename,
				$conf
			);
			
			// Print progress
			if ($this->verbose) {
				$current = $i + 1;
				print sprintf("[%u/%u] %s\n", $current, $total, $destination_single);
			}
		}
		
		// Done!
		if ($this->verbose) {
			$file_string = 'files';
			
			if ($total === 1)
				$file_string = 'file';
			
			$execution_time = (microtime(true) - $start_time) * 1000;
			
			print sprintf("\nDone! %u %s converted in %f milliseconds.\n", $total, $file_string, $execution_time);
		}
	}
}