<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die(http_response_code(403));

$arComponentDescription = array(
    'NAME' => GetMessage('C_NAME'),
    'DESCRIPTION' => GetMessage('C_DESCRIPTION'),
    'PATH' => array(
        'ID' => GetMessage('C_ID'),
        'CHILD' => array(
            'ID' => 'weather.in.city',
            'NAME' => GetMessage('C_NAME')
        )
    )
);