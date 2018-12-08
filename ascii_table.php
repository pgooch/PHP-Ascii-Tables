<?php
/**
 * PHP ASCII Tables
 *
 * This class will convert multi-dimensional arrays into ASCII Tables, and vice-versa.
 *
 * @package   PHP-Ascii-Tables
 * @author    Phillip Gooch <phillip.gooch@gmail.com>
 * @copyright 2018 Phillip Gooch
 * @link      https://github.com/pgooch/PHP-Ascii-Tables
 */
class Ascii_Table
{
    /**
     * These are all the variables that the script uses to build out the table, none of them meant for user-modification.
     */

    /**
     * An array that contains the max character width of each column (not including buffer spacing).
     * @access private
     */
    private $col_widths = array();

    /**
     * An array that contains the column types.
     * @access private
     */
    private $col_types = array();

    /**
     * The complete width of the table, including spacing and bars.
     * @access private
     */
    private $table_width = 0;

    /**
     * The error reported by file_put_contents or file_get_contents when it fails a save or load attempt.
     * @access public
     */
    public $error = '';

    /**
     * This is the function that you will call to make the table. You must pass it at least the first variable.
     *
     * @param array $array A multi-dimensional array containing the data you want to build a table from.
     * @param string $title The title of the table that will be centered above it, if you do not want a title you can pass a blank.
     * @param boolean $return The method of returning the data, this has 3 options.
     *                        True - The script will return the table as a string. (Required).
     *                        False - The script will echo the table out, nothing will be returned.
     *                        String - It will attempt to save the table to a file with the strings name, Returning true/false of success or fail.
     * @param boolean $autoalign_cells If True all column names and values with numeric data types will be aligned to the right of the cell.
     *
     * @return string|bool|void The ASCII table representation of $array.
     */
    public function make_Table($array, $title = '', $return = false, $autoalign_cells = false)
    {
        // First things first lets get the variable ready
        $table = '';
        $this->col_widths = array();
        $this->col_types = array();
        $this->table_width = 0;

        // Modify the table to support any line breaks that might exist
        $modified_array = array();
        foreach ($array as $row => $row_data) {
            // This will break the cells up on line breaks and store them in $raw_array with the longest value for that column in $longest_cell
            $row_array = array();
            $longest_cell = 1;
            foreach ($row_data as $cell => $cell_value) {
                $cell_value = explode("\n", $cell_value);
                $row_array[$cell] = $cell_value;
                $longest_cell = max($longest_cell, count($cell_value));
            }

            // This will loop as many times as the longest, if there is a value it will use that, if not it will just give it an empty string
            for ($i = 0; $i < $longest_cell; $i++) {
                $new_row_temp = array();
                foreach ($row_array as $col => $col_data) {
                    if (isset($col_data[$i])) {
                        $new_row_temp[$col] = trim($col_data[$i]);
                    } else {
                        $new_row_temp[$col] = '';
                    }
                }
                $modified_array[] = $new_row_temp;
            }
        }

        // Finally we can call the fully modified array the array for future use
        $array = $modified_array;

        // Now we need to get some details prepared.
        $this->getColWidths($array);
        $this->getColTypes($array);

        // If there is going to be a title we are also going to need to determine the total width of the table, otherwise we don't need it
        if ($title != '') {
            $this->getTableWidth();
            $table .= $this->makeTitle($title);
        }

        // If we have a blank array then we don't need to output anything else
        if (isset($array[0])) {
            // Now we can output the header row, along with the divider rows around it
            $table .= $this->makeDivider();

            // Output the header row
            $table .= $this->makeHeaders($autoalign_cells);

            // Another divider line
            $table .= $this->makeDivider();

            // Add the table data in
            $table .= $this->makeRows($array, $autoalign_cells);

            // The final divider line.
            $table .= $this->makeDivider();
        }

        // Now handle however you want this returned
        // First if it's a string were saving
        if (is_string($return)) {
            $save = @file_put_contents($return, $table);
            if ($save) {

                return true;
            } else {
                // Add the save_error if there was one
                $this->error = 'Unable to save table to "' . $return . '".';

                return false;
            }
        } else {
            // the bool returns are very simple.
            if ($return) {

                return $table;
            } else {
                echo $table;
            }
        }
    }

    /**
     * This function will load a saved ascii table and turn it back into a multi-dimensional table.
     *
     * @param string $table A PHP ASCII Table either as a string or a text file.
     *
     * @return array Return a multi-dimensional array similar to the one that you would have given it `make_table()` to create it.
     */
    public function break_Table($table)
    {
        // Try and load the file, if it fails then just return false and set an error message
        $load_file = @file_get_contents($table);
        if ($load_file !== false) {
            $table = $load_file;
        }

        // First thing we want to do is break it apart at the lines
        $table = explode(PHP_EOL, trim($table));

        // Check if the very first character of the very first row is a +, if not delete that row, it must be a title.
        if (substr($table[0], 0, 1) != '+') {
            unset($table[0]);
            $table = array_values($table);
        }

        // Were going to need a few variables ready-to-go, so lets do that
        $array = array();
        $array_columns = array();

        // Now we want to grab row [1] and get the column names from it.
        $columns = explode('|', $table[1]);

        foreach ($columns as $n => $value) {
            // The first and last columns are blank, so lets skip them
            if ($n > 0 && $n < count($columns) - 1) {
                // If we have a value after trimming the whitespace then use it, otherwise just give the column it's number as it's name
                if (trim($value) != '') {
                    $array_columns[$n] = trim($value);
                } else {
                    $array_columns[$n] = $n;
                }
            }
        }

        // And now we can go through the bulk of the table data
        for ($row = 3; $row < count($table) - 1; $row++) {
            // Break the row apart on the pipe as well
            $row_items = explode('|', $table[$row]);

            // Loop through all the array columns and grab the appropriate value, placing it all in the $array variable.
            foreach ($array_columns as $pos => $column) {
                // Add the details into the main $array table, remembering to trim them of that extra whitespace
                $array[$row][$column] = trim($row_items[$pos]);
            }
        }

        // Reflow the array so that it starts at the logical 0 point
        $array = array_values($array);

        // Return the array
        return $array;
    }

    /**
     * This will take a table in either a file or a string and scrape out two columns of data from it. If you only pass a single column it will return that in a straight numeric array.
     *
     * @param string $table The table file or string.
     * @param string $key They column to be used as the array key, if no value is passed, the value that will be placed in the numeric array.
     * @param string $value The column to be used as the array value, if null then key will be returned in numeric array.
     *
     * @return array Return they key/value pairs requested.
     */
    public function scrape_Table($table, $key, $value = null)
    {
        // First things first wets parse the entire table out.
        $table = $this->break_Table($table);

        // Set up a variable to store the return in while processing
        $array = array();

        // Now we loop through the table
        foreach ($table as $row => $data) {
            // If value is null then set it to key and key to row.
            if ($value == null) {
                $grabbed_value = $data[$key];
                $grabbed_key = $row;
                // Else just grab the desired key/value values
            } else {
                $grabbed_key = $data[$key];
                $grabbed_value = $data[$value];
            }

            // Place the information into the array().
            $array[$grabbed_key] = $grabbed_value;
        }

        // Finally return the new array
        return $array;
    }

    /**
     * This function will use the mb_strlen if available or strlen.
     *
     * @param string $col_value The string that be need to be counted.
     *
     * @return int Returns a lenght of string using mb_strlen or strlen.
     */
    private static function len($col_value)
    {
        return extension_loaded('mbstring') ? mb_strlen($col_value) : strlen($col_value);
    }

    /**
     * This method will set the $col_width variable with the longest value in each column.
     *
     * @param array $array The multi-dimensional array you are building the ASCII Table from.
     *
     * @return void
     */
    private function getColWidths($array)
    {
        // If we have some array data loop through each row, then through each cell
        if (isset($array[0])) {
            foreach (array_keys($array[0]) as $col) {
                // Get the longest col value and compare with the col name to get the longest
                $this->col_widths[$col] = max(max(array_map(array($this, 'len'), $this->arrCol($array, $col))), $this->len($col));
            }
        }
    }

    /**
     * This method will set the $col_types variable with the type of value in each column.
     *
     * @param array $array The multi-dimensional array you are building the ASCII Table from.
     *
     * @return void
     */
    private function getColTypes($array)
    {
        // If we have some array data loop through each row, then through each cell
        if (isset($array[0])) {
            // Parse each col and each row to get the column type
            foreach (array_keys($array[0]) as $col) {
                foreach ($array as $i => $row) {
                    if (trim($row[$col]) != '') {
                        if (!isset($this->col_types[$col])) {
                            $this->col_types[$col] = is_numeric($row[$col]) ? 'numeric' : 'string';
                        } else {
                            if ($this->col_types[$col] == 'numeric') {
                                $this->col_types[$col] = is_numeric($row[$col]) ? 'numeric' : 'string';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * This is an array_column shim, it will use the PHP array_column function if there is one, otherwise it will do the same thing the old way.
     *
     * @param array $array The multi-dimensional array you are building the ASCII Table from.
     * @param string $col A table's key (column).
     *
     * @return array An array containing all values of a column.
     */
    private function arrCol($array, $col)
    {
        if (is_callable('array_column')) {
            $return = array_column($array, $col);
        } else {
            $return = array();
            foreach ($array as $n => $dat) {
                if (isset($dat[$col])) {
                    $return[] = $dat[$col];
                }
            }
        }

        return $return;
    }

    /**
     * This will get the entire width of the table and set $table_width accordingly. This value is used when building.
     *
     * @return void
     */
    private function getTableWidth()
    {
        // Add up all the columns
        $this->table_width = array_sum($this->col_widths);

        // Add in the spacers between the columns (one on each side of the value)
        $this->table_width += count($this->col_widths) * 2;

        // Add in the dividers between columns, as well as the ones for the outside of the table
        $this->table_width += count($this->col_widths) + 1;
    }

    /**
     * This will return the centered title (only called if a title is passed).
     *
     * @param string $title The table's title.
     *
     * @return string The centered title.
     */
    private function makeTitle($title)
    {
        // First we want to remove any extra whitespace for a proper centering
        $title = trim($title);

        // Determine the padding needed on the left side of the title
        $left_padding = floor(($this->table_width - $this->len($title)) / 2);

        // return exactly what is needed
        return str_repeat(' ', max($left_padding, 0)) . $title . PHP_EOL;
    }

    /**
     * This will use the data in the $col_width var to make a divider line.
     *
     * @return string A table's divider.
     */
    private function makeDivider()
    {
        // were going to start with a simple union piece
        $divider = '+';

        // Loop through the table, adding lines of the appropriate length (remembering the +2 for the spacers), and a union piece at the end
        foreach ($this->col_widths as $col => $length) {
            $divider .= str_repeat('-', $length + 2) . '+';
        }

        // return it
        return $divider . PHP_EOL;
    }

    /**
     * This will look through the $col_widths array and make a column header for each one.
     *
     * @param bool $autoalign_cells If True, columns with numeric data types will be aligned to the right of the cell.
     *
     * @return string The row of the table header.
     */
    private function makeHeaders($autoalign_cells)
    {
        // This time were going to start with a simple bar;
        $row = '|';

        // Loop though the col widths, adding the cleaned title and needed padding
        foreach ($this->col_widths as $col => $length) {
            // Add title
            $alignment = $autoalign_cells && isset($this->col_types[$col]) && $this->col_types[$col] == 'numeric' ? STR_PAD_LEFT : STR_PAD_RIGHT;
            $row .= ' ' . str_pad($col, $this->col_widths[$col], ' ', $alignment) . ' ';

            // Add the right hand bar
            $row .= '|';
        }

        // Return the row
        return $row . PHP_EOL;
    }

    /**
     * This makes the actual table rows.
     *
     * @param array $array The multi-dimensional array you are building the ASCII Table from.
     * @param bool $autoalign_cells If True, column values with numeric data types will be aligned to the right of the cell.
     *
     * @return string The rows of the table.
     */
    private function makeRows($array, $autoalign_cells)
    {
        // Just prep the variable
        $rows = '';

        // Loop through rows
        foreach ($array as $n => $data) {
            // Again were going to start with a simple bar
            $rows .= '|';

            // Loop through the columns
            foreach ($data as $col => $value) {
                // Add the value to the table
                $alignment = $autoalign_cells && isset($this->col_types[$col]) && $this->col_types[$col] == 'numeric' ? STR_PAD_LEFT : STR_PAD_RIGHT;
                $rows .= ' ' . str_pad($value, $this->col_widths[$col], ' ', $alignment) . ' ';

                // Add the right hand bar
                $rows .= '|';
            }

            // Add the row divider
            $rows .= PHP_EOL;
        }

        // Return the row
        return $rows;
    }
}
