<?php

function runScripts($path, &$data)
{
    $fileType = pathinfo($path, PATHINFO_EXTENSION);

    $command = '';

    switch ($fileType) {
        case 'php':
            $command .= 'php ' . $path;
            break;
        case 'py':
            $command .= 'python ' . $path;
            break;
        default:
            break;
    }
    
    $output = [];
    $return_val = '';

    exec($command, $output, $return_val);

    if ($return_val == 0) {
        $result = testOutput($output);
        $temp = [
            'command' => $command,
            'result' => $result
        ];
        $data[] = $temp;
    }
}

function testOutput($output)
{
    $pattern = "/Hello World, this is .* with HNGi7 ID .* for stage 2 task/";

    if (preg_match($pattern, $output[0])) {
        return "passed";
    }
    return "Failed";
}

// Start of script

$scripts = scandir('./scripts');

foreach ($scripts as $script) {
    if (! in_array($script, ['.', '..'])) {
        runScripts('./scripts/' . $script, $data);
    }
}

$display = $_SERVER['QUERY_STRING'] ?? 'html';
$display = $display == 'json' ? 'json' : 'html';

if ($display == 'html') {
    echo '<h1>Task1</h1>';
    foreach ($data as $row) {
        echo '<p>"' . $row['command'] . '": ' . $row['result'] . '</p>';
    }
} elseif ($display == 'json') {
    $json = json_encode($data);
    header('Content-Type: application/json');
    echo $json;
}
