<?php
header('Content-Type: text/json; charset=utf-8');
$content=file_get_contents(__DIR__ . '/content.json');
echo $content;
// $content=<<<'CON'
// [{
//     "id": "1962308756",
//     "name": "General Interest",
//     "status": "ACTIVE",
//     "created_date": "2016-12-31T12:54:26.000Z",
//     "modified_date": "2016-12-31T12:54:26.000Z",
//     "contact_count": 1
// }, {
//     "id": "1586385249",
//     "name": "cms90 first list",
//     "status": "ACTIVE",
//     "created_date": "2016-12-31T14:17:53.000Z",
//     "modified_date": "2016-12-31T14:17:53.000Z",
//     "contact_count": 1
// }]
// CON;
// $content=str_replace(PHP_EOL,'',$content);
// $json_fields=json_decode($content);
// $results=array();
// foreach ($json_fields as $key => $field) {
//     $results[]=(array)$field;
// }
// echo $results;