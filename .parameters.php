<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die(http_response_code(403));

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'API_KEY' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('PARAM_API_KEY'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => ''
        ),
        'CITY_NAME' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('PARAM_CITY_NAME'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => ''
        ),
        'VIEW' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('PARAM_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => array(
                'INFORMER' => GetMessage('PARAM_VIEW_VALUE_INFORMER'),
                'CHART' => GetMessage('PARAM_VIEW_VALUE_CHART')
            ),
            'DEFAULT' => 'INFORMER',
            'ADDITIONAL_VALUES' => 'N',
            'REFRESH' => 'Y'
        ),
        'CACHE_TIME' => array(
            'DEFAULT' => 43200
        )
    )
);