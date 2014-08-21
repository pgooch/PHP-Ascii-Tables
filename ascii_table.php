<?php
/*
	PHP ASCII Tables

	This class will convert multi-dimensional arrays into ASCII Tabled, and vise-versa.

	By Phillip Gooch <phillip.gooch@gmail.com>
*/
class ascii_table{
	/*
		These are all the variables that the script uses to build out the table, none of them meant for user-modification

		$col_widths 	- An array that contains the max character width of each column (not including buffer spacing)
		$table_width 	- The complete width of the table, including spacing and bars
		$error 			- The error reported by file_put_contents or file_get_contents when it fails a save or load attempt.
	*/
	private $col_widths = array();
	private $table_width = 0;
	public  $error = '';

	/*
		This is the function that you will call to make the table. You must pass it at least the first variable

		$array 	- A multi-dimensional array containing the data you want to build a table from.
		$title 	- The title of the table that will be centered above it, if you do not want a title you can pass a blank
		$return - The method of returning the data, this has 3 options.
			True 	- The script will return the table as a string. (Required)
			False 	- The script will echo the table out, nothing will be returned.
			String 	- It will attempt to save the table to a file with the strings name, Returning true/false of success or fail.
	*/ 
	function make_table($array,$title='',$return=false){

		// First things first lets get a variable ready to put the table into
		$table = '';

		// Now we need to get some details prepared.
		$this->get_col_widths($array);

		// If there is going to be a title we are also going to need to determine the total width of the table, otherwise we don't need it
		if($title!=''){
			$this->get_table_width();
			$table .= $this->make_title($title);
		}

		// Now we can output the header row, along with the divider rows around it
		$table .= $this->make_divider();

		// Output the header row
		$table .= $this->make_headers();

		// Another divider line
		$table .= $this->make_divider();

		// Add the table data in
		$table .= $this->make_rows($array);

		// The final divider line.
		$table .= $this->make_divider();

		// Now handle however you want this returned
		// First if it's a string were saving
		if(is_string($return)){
			$save = @file_put_contents($return,$table);
			if($save){
				return true;
			}else{
				// Add the save_error if there was one
				$this->error = 'Unable to save table to "'.$return.'".';
				return false;
			}
		}else{
			// the bool returns are very simple.
			if($return){
				return $table;
			}else{
				echo $table;
			}
		}

	}

	/*
		This function will load a saved ascii table and turn it back into a multi-dimensional table.

		$table 	- A PHP ASCII Table either as a string or a text file

		Returns a multi-dimensional array;
	*/ 
	function break_table($table){

		// Try and load the file, if it fails then just return false and set an error message
		$load_file = @file_get_contents($table);
		if($load_file!==false){
			$table = $load_file;
		}

		// First thing we want to do is break it apart at the lines
		$table = explode("\n",trim($table));

		// Check if the very first character of the very first row is a +, if not delete that row, it must be a title.
		if(substr($table[0],0,1)!='+'){
			unset($table[0]);
			$table = array_values($table);
		}

		// Were going to need a few variables ready-to-go, so lets do that
		$array = array();
		$array_columns = array();

		// Now we want to grab row [1] and get the column names from it.
		$columns = explode('|',$table[1]);

		foreach($columns as $n => $value){

			// The first and last columns are blank, so lets skip them
			if($n>0 && $n<count($columns)-1){

				// If we have a value after trimming the whitespace then use it, otherwise just give the column it's number as it's name
				if(trim($value)!=''){
					$array_columns[$n] = trim($value);
				}else{
					$array_columns[$n] = $n;
				}

			}

		}

		// And now we can go through the bulk of the table data
		for($row=3;$row<count($table)-1;$row++){

			// Break the row apart on the pipe as well
			$row_items = explode('|',$table[$row]);

			// Loop through all the array columns and grab the appropriate value, placing it all in the $array variable.
			foreach($array_columns as $pos => $column){

				// Add the details into the main $array table, remembering to trim them of that extra whitespace
				$array[$row][$column] = trim($row_items[$pos]);

			}

		}

		// Reflow the array so that it starts at the logical 0 point
		$array = array_values($array);

		// Return the array
		return $array;

	}

	/*
		This will take a table in either a file or a string and scrape out two columns of data from it. If you only pass a single column it will 
		return that in a straight numeric array.

		$table - The table file or string
		$key - They column to be used as the array key, if no value is passed, the value that will be placed in the numeric array.
		$value - the column to be used as the array value, if null then key will be returned in numeric array.
	*/
	function scrape_table($table,$key,$value=null){

		// First things first wets parse the entire table out.
		$table = $this->break_table($table);

		// Set up a variable to store the return in while processing
		$array = array();

		// Now we loop through the table
		foreach($table as $row => $data){

			// If value is null then set it to key and key to row.
			if($value==null){
				$grabbed_value = $data[$key];
				$grabbed_key = $row;
			
			// Else just grab the desired key/value values
			}else{
				$grabbed_key = $data[$key];
				$grabbed_value = $data[$value];
			}

			// Place the information into the array().
			$array[$grabbed_key] = $grabbed_value;

		}

		// Finally return the new array
		return $array;

	}

	/*
		This class will set the $col_width variable with the longest value in each column

		$array 	- The multi-dimensional array you are building the ASCII Table from
	*/
	function get_col_widths($array){

		// Loop through each row, then through each cell
		foreach($array as $row_num => $row){
			foreach($row as $col => $value){

				// Make sure the col is in col_widths, if not then add it in and make col header the de-facto longest
				if(!isset($this->col_widths[$col])){
					$this->col_widths[$col] = strlen($col);
				}

				// Check if this column is the longest, if so update $col_width
				else if($this->col_widths[$col] < strlen($value)){
					$this->col_widths[$col] = strlen($value);
				}

			}
		}

	}

	/*
		This will get the entire width of the table and set $table_width accordingly. This value is used when building.
	*/
	function get_table_width(){

		// Add up all the columns
		$this->table_width = array_sum($this->col_widths);

		// Add in the spacers between the columns (one on each side of the value)
		$this->table_width += count($this->col_widths)*2;

		// Add in the dividers between columns, as well as the ones for the outside of the table
		$this->table_width += count($this->col_widths)+1;

	}

	/*
		This will return the centered title (only called if a title is passed)
	*/
	function make_title($title){

		// First we want to remove any extra whitespace for a proper centering
		$title = trim($title);

		// Determine the padding needed on the left side of the title
		$left_padding = floor(($this->table_width-strlen($title))/2);

		// return exactly what is needed
		return str_repeat(' ',$left_padding).$title."\n";
	}

	/*
		This will use the data in the $col_width var to make a divider line.
	*/
	function make_divider(){

		// were going to start with a simple union piece
		$divider = '+';

		// Loop through the table, adding lines of the appropriate length (remembering the +2 for the spacers), and a union piece at the end
		foreach($this->col_widths as $col => $length){
			$divider .= str_repeat('-',$length+2).'+';
		}

		// return it
		return $divider."\n";

	}

	/*
		This will look through the $col_widths array and make a column header for each one
	*/
	function make_headers(){

		// This time were going to start with a simple bar;
		$row = '|';

		// Loop though the col widths, adding the cleaned title and needed padding
		foreach($this->col_widths as $col => $length){

			// Add title
			$row .= ' '.$col.' ';

			// Check and see if we need additional padding, if so go ahead and add it
			if(strlen($col)<$length){
				$row .= str_repeat(' ',$length-strlen($col));
			}

			// Add the right hand bar
			$row .= '|';

		}

		// Return the row
		return $row."\n";

	}

	/*
		This makes the actual table rows

		$array 	- The multi-dimensional array you are building the ASCII Table from
	*/
	function make_rows($array){

		// Just prep the variable
		$rows = '';

		// Loop through rows
		foreach($array as $n => $data){

			// Again were going to start with a simple bar
			$rows .= '|';

			// Loop through the columns
			foreach($data as $col => $value){

				// Add the value to the table
				$rows .= ' '.$value.' ';

				// check and see if that value needs any padding, if so add it
				if(strlen($value)<$this->col_widths[$col]){
					$rows .= str_repeat(' ',$this->col_widths[$col]-strlen($value));
				}

				// Add the right hand bar
				$rows .= '|';

			}

			// Add the row divider
			$rows .= "\n";

		}

		// Return the row
		return $rows;

	}

}