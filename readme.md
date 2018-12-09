# PHP ASCII-Table

This class will convert multi-dimensional arrays into ASCII Tables, and vice-versa.

### Setup & Use
Include `ascii_table.php` and call a new instance of the object. You can then call one of three functions:

- `make_Table($array, [$title], [$return], [$autoalign_cells])` will make a table with the multi-dimensional `$array` passed. Additionally you can specify an optional `$title` that will be centered on a line above the table. The `$return` variable gives you 3 options; `True` will return the table as an array, `False` will output the array directly, and a `String` will attempt to save the array in a text file with the given name/location and will return true/false upon completlion. If false the error message will be logged to the `$error` class variable. The parameter `$autoalign_cells`, if set to `True`, all columns containing only numeric datatypes will be aligned to the right of the cell.
- `break_Table($table)` Takes an table or a filename that containing a text file output from `make_table()` and will return a multi-dimensional array similar to the one that you would have given it `make_table()` to create it.
- `scrape_Table($table, $key, [$value])` will take a table or link to a file containing a table as `break_table()` does but will only return they key/value pairs you request. If you do not include the value it will use the key as the value and return it in a numeric array. It should be noted that if you use both key and value that muliple keys will overrite eachother and the returning array will only contain the last one in the table.


### Examples
Examples of the classes functionality can be found in examples.php, and a text output can be found in `example.txt`. Table will be output or returned like this:

                                           Colors in Various Formats
    +--------+---------+-----+-------+------+-----+------------+-----------+------+---------+--------+-----+
    | color  | HEX     | Red | Green | Blue | Hue | Saturation | Lightness | Cyan | Magenta | Yellow | Key |
    +--------+---------+-----+-------+------+-----+------------+-----------+------+---------+--------+-----+
    | Red    | #FF0000 | 255 | 0     | 0    | 0   | 100        | 50        | 0    | 100     | 100    | 0   |
    | Orange | #FFA500 | 255 | 165   | 0    | 39  | 100        | 50        | 0    | 100     | 35     | 0   |
    | Yellow | #FFFF00 | 255 | 255   | 0    | 60  | 100        | 50        | 0    | 0       | 100    | 0   |
    | Green  | #008000 | 0   | 128   | 0    | 120 | 100        | 25        | 100  | 0       | 100    | 50  |
    | Blue   | #0000FF | 0   | 0     | 255  | 240 | 100        | 50        | 100  | 100     | 0      | 0   |
    | Indigo | #4B0082 | 75  | 0     | 130  | 275 | 100        | 25        | 42   | 100     | 0      | 49  |
    | Violet | #EE82EE | 238 | 130   | 238  | 300 | 76         | 72        | 0    | 45      | 0      | 7   |
    +--------+---------+-----+-------+------+-----+------------+-----------+------+---------+--------+-----+
