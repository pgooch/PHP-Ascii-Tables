<?php
class ascii_table{
	// These will be filled for you once the class is called
	private $cols = array();
	private $total_cols = 0;
	private $table_width = 0;
	private $title = '';
	private $wrapped = true; // This determines if the table is wrapped
	function __construct($array,$title=''){
		// Prep for display
		$this->determine_cols($array);
		$this->table_width = array_sum($this->cols)+($this->total_cols*3)+1;
		// This is where we start outputting the table.
		$this->output_title($title);
		$this->output_horizontal_line();
		$this->output_header_row();
		$this->output_horizontal_line();
		$this->output_data($array);
		$this->output_horizontal_line();
	}
	// Gets some base information regarding the columns and their contents (lengths mostly);
	function determine_cols($array){
		// This gets the longest length per column
		foreach($array as $g => $data){
			foreach($data as $col => $cell){
				if(!isset($this->cols[$col])){
					$this->cols[$col] = strlen($col);
				}
				if(strlen($cell)>$this->cols[$col]){
					$this->cols[$col] = strlen($cell);
				}
			}
		}
		// This is just the number of columns
		$this->total_cols = count($this->cols);
	}
	// This will output a given array as a line
	function output_row($array,$intersections=false){
		if($intersections){
			echo '+-';
		}else{
			echo '| ';
		}
		$row = 0;
		foreach($array as $col => $value){
			$row++;
			echo $value;			
			if($this->total_cols>$row){
				if($intersections){
					echo '-+-';
				}else{
					echo ' | ';
				}
			}else{
				if($intersections){
					echo '-+';
				}else{
					echo ' |';
				}
				echo "\n";
			}
		}
	}
	// This will output the title if there is one
	function output_title($title){
		if($title!=''){
			$title_lenth = strlen($title);
			$padding = $this->table_width-$title_lenth;
			if($padding<0){
				$padding=0;
			}else{
				$padding = floor($padding/2);
			}
			// Output title
			echo str_repeat(' ',$padding).$title."\n";
		}
	}
	// This outputs a basic horizontal line with appropriate dividers between columns
	function output_horizontal_line(){
		$divider = array();
		foreach($this->cols as $col => $len){
			$divider[$col] = str_repeat('-',$len);
		}
		echo $this->output_row($divider,true);
	}
	// This will output the col names for the header row
	function output_header_row(){
		$header = array();
		foreach($this->cols as $col => $len){
			// Clean the col title a bit
			$col = ucwords(strtolower(str_ireplace('_',' ',$col)));
			// Output Col
			$header[$col] = sprintf('%-'.$len.'s',$col);
		}
		echo $this->output_row($header);
	}
	// This loops through the data and outputs each row
	function output_data($array){
		foreach($array as $g => $data){
			$row = array();
			foreach($this->cols as $col => $len){
				if(!isset($data[$col])){$data[$col]='';}// Just in case a cell is missing
				$row[$col] = sprintf('%-'.$len.'s',$data[$col]);
			}
			echo $this->output_row($row);
		}
	}
}