<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>PHP ASCII-Table - Seattle Zip Codes Example.</title>
    </head>
    <body>
        <?php
        require_once('../ascii_table.php');
        // This is a really basic CSV to multi-dimensional array converter, it 
        // should not be used in anything more than this basic example. 
        $zip_codes_raw = file_get_contents('seattle-zip-codes.csv');
        $zip_codes_raw = explode("\n",$zip_codes_raw);
        $zip_codes_keys = explode(',',$zip_codes_raw[0]);
        unset($zip_codes_raw[0]);
        foreach($zip_codes_keys as $k => $v){
            $zip_codes_keys[$k] = trim(trim($v),'"');
        }
        foreach($zip_codes_raw as $n => $data){
            $data = explode(',',$data);
            foreach($data as $k => $v){
                $v = trim(trim($v),'"');
                if($v!=''&&$v!=0){
                    if($k>=4){
                        $v = number_format($v);
                    }
                    $zip_codes[$n][$zip_codes_keys[$k]] = $v;
                }
            }
        } ?>
        <pre><?php new ascii_table($zip_codes,'Seattle Zip Codes') ?></pre>
   </body>
</html>