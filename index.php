<?php

function getCommand($path)
{
    $fileType = pathinfo($path, PATHINFO_EXTENSION);

    $command = '';

    switch ($fileType) {
        case 'php':
            $command .= 'php ' . $path;
            break;
        case 'py':
            $command .= 'python3 ' . $path;
            break;
        case 'js':
            $command .= 'node ' . $path;
            break;
        default:
            break;
    }

    return $command;
}

function runScripts($script_paths, $display)
{
    if ($display == 'html') {
        echo '<h1>Task1</h1>';
    }


    $data = array();

    foreach ($script_paths as $script_path) {
        $temp = array();
        $temp['HNGID'] = strtoupper(pathinfo($script_path, PATHINFO_FILENAME));
        $temp['Command'] = getCommand($script_path);

        $data[] = $temp;
    }
    
    foreach ($data as &$script) {
        $output = [];
        $return_val = '';

        exec($script['Command'], $output, $return_val);

        if ($return_val == 0) {
            $script['Comment'] = $output[0];
            $script['Result'] = testOutput($output);
        }

        if ($display == 'html') {
            flushHTMLOut($script);
        }
    }

    if ($display == 'json') {
        $json = json_encode($data, true);
        header('Content-Type: application/json');
        echo $json;
    }
}

function testOutput($output)
{
    $pattern = "/Hello World, this is .* with HNGi7 ID .* using .* for stage 2 task/";

    if (preg_match($pattern, $output[0])) {
        return "Passed";
    }
    return "Failed";
}

function flushHTMLOut($script)
{
        echo '<p>"HNGID": '.$script['HNGID'].',  "Comment": '. $script['Comment'].',  "Status":'.$script['Result'].'</p>';
        ob_flush();
        flush();
        sleep(1);
}


//
// Start of script
//

$display = $_SERVER['QUERY_STRING'] ?? 'html';
$display = $display == 'json' ? 'json' : 'html';

$script_paths = array();

foreach (scandir('./scripts') as $script) {
    if (! in_array($script, ['.', '..'])) {
        $script_paths[] = './scripts/' . $script;
    }
}

runScripts($script_paths, $display);
