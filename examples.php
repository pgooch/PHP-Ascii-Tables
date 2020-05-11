<?php
require_once 'vendor/autoload.php';

$ascii_table = new Ascii_Table\Ascii_Table();
// Colors Example Data
$svg_colors = array(
   array(
        'color' => 'Red',
        'HEX' => '#FF0000',
        'Red' => '255',
        'Green' => '0',
        'Blue' => '0',
        'Hue' => '0',
        'Saturation' => '100',
        'Lightness' => '50',
        'Cyan' => '0',
        'Magenta' => '100',
        'Yellow' => '100',
        'Key' => '0',
    ),
    array(
        'color' => 'Orange',
        'HEX' => '#FFA500',
        'Red' => '255',
        'Green' => '165',
        'Blue' => '0',
        'Hue' => '39',
        'Saturation' => '100',
        'Lightness' => '50',
        'Cyan' => '0',
        'Magenta' => '100',
        'Yellow' => '35',
        'Key' => '0',
    ),
    array(
        'color' => 'Yellow',
        'HEX' => '#FFFF00',
        'Red' => '255',
        'Green' => '255',
        'Blue' => '0',
        'Hue' => '60',
        'Saturation' => '100',
        'Lightness' => '50',
        'Cyan' => '0',
        'Magenta' => '0',
        'Yellow' => '100',
        'Key' => '0',
    ),
    array(
        'color' => 'Green',
        'HEX' => '#008000',
        'Red' => '0',
        'Green' => '128',
        'Blue' => '0',
        'Hue' => '120',
        'Saturation' => '100',
        'Lightness' => '25',
        'Cyan' => '100',
        'Magenta' => '0',
        'Yellow' => '100',
        'Key' => '50',
    ),
    array(
        'color' => 'Blue',
        'HEX' => '#0000FF',
        'Red' => '0',
        'Green' => '0',
        'Blue' => '255',
        'Hue' => '240',
        'Saturation' => '100',
        'Lightness' => '50',
        'Cyan' => '100',
        'Magenta' => '100',
        'Yellow' => '0',
        'Key' => '0',
    ),
    array(
        'color' => 'Indigo',
        'HEX' => '#4B0082',
        'Red' => '75',
        'Green' => '0',
        'Blue' => '130',
        'Hue' => '275',
        'Saturation' => '100',
        'Lightness' => '25',
        'Cyan' => '42',
        'Magenta' => '100',
        'Yellow' => '0',
        'Key' => '49',
    ),
    array(
        'color' => 'Violet',
        'HEX' => '#EE82EE',
        'Red' => '238',
        'Green' => '130',
        'Blue' => '238',
        'Hue' => '300',
        'Saturation' => '76',
        'Lightness' => '72',
        'Cyan' => '0',
        'Magenta' => '45',
        'Yellow' => '0',
        'Key' => '7',
    ),
);
$multiline_test_data = array(
    array(
        'id' => 1,
        'multiline' => "This cell \n is exactly \n 3 lines.",
        'ellipse' => '...'
    ),
    array(
        'id' => 2,
        'multiline' => "This cell is only \n 2 lines",
        'ellipse' => '...'
    ),
    array(
        'id' => 3,
        'multiline' => "Just a single line",
        'ellipse' => '...'
    ),
    array(
        'id' => 4,
        'multiline' => "Also a single line",
        'ellipse' => ".\n.\n."
    ),
);
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>PHP ASCII-Table Examples</title>
    </head>
    <body>
        <pre>
<?php $ascii_table->make_table($multiline_test_data,'Multi-line Cells') ?>

<?php 
$table = $ascii_table->make_table($svg_colors,'Colors in Various Formats',true);
echo $table; ?>

The color names and hex values, scraped from the "Colors in Various Formats" table above.
<?php print_r($ascii_table->scrape_table($table,'color','HEX')); ?>

All of the zip codes in Seattle, broken from the example.txt file.
<?php print_r($ascii_table->break_table('./example.txt')); ?>

What happens when you try and pass an empty array to the make_table function.
<?php $ascii_table->make_table(array(),'Blank Test'); ?>
        </pre>
   </body>
</html>