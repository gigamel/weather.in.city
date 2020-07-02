<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die(http_response_code(403));

if (empty($arParams['CITY_NAME']) || empty($arParams['API_KEY']))
    return;

switch ($arParams['VIEW']) {
    case 'CHART':
        $componentPage = 'chart';
        break;
    default:
        $componentPage = 'informer';
        break;
}

require_once __DIR__.'/class.php';
$class = WeatherInCityComponent::getInstance($arParams['API_KEY']);

if ($this->StartResultCache(false)) {
    $jsonResult = $class->request($arParams['CITY_NAME']);
    
    if ($jsonResult === false) {
        $this->AbortResultCache();
        return;
    }
    
    $arResult = array(
        'ITEMS' => array(),
        'CITY' => array(
            'ID' => $jsonResult['city']['id'],
            'NAME' => $jsonResult['city']['name'],
            'LAT' => $jsonResult['city']['coord']['lat'],
            'LON' => $jsonResult['city']['coord']['lon'],
            'COUNTRY' => $jsonResult['city']['coord']['country'],
            'POPULATION' => $jsonResult['city']['population'],
            'TIMEZONE' => $jsonResult['city']['timezone'],
            'SUNRIZE' => $jsonResult['city']['sunrise'],
            'SUNSET' => $jsonResult['city']['sunset']
        ),
        'RESPONSE' => $jsonResult
    );
    
    switch ($arParams['VIEW']) {
        // Гистограмма
        case 'CHART':
            $arForecast = array();
            foreach ($jsonResult['list'] as $arItem) {
                $arDate = explode(' ', $arItem['dt_txt']);
                
                if (!isset($arForecast[$arDate[0]])) {
                    $arForecast[$arDate[0]] = array(
                        'TEMP_AFTERNOON' => array(),
                        'TEMP_NIGHT' => array()
                    );
                }
                
                if ($class->isAfternoonTime($arItem['dt_txt'])) {
                    $arForecast[$arDate[0]]['TEMP_AFTERNOON'][] = $class->toTempC($arItem['main']['temp']);
                }
                
                if ($class->isNightTime($arItem['dt_txt'])) {
                    $arForecast[$arDate[0]]['TEMP_NIGHT'][] = $class->toTempC($arItem['main']['temp']);
                }
            }
            
            foreach ($arForecast as $dateString => $arItem) {
                if (empty($arItem['TEMP_AFTERNOON']) || empty($arItem['TEMP_NIGHT'])) {
                    continue;
                }
                
                $afternoonTemp = 0;
                foreach ($arItem['TEMP_AFTERNOON'] as $value) {
                    $afternoonTemp += $value;
                }
                $afternoonTemp = $afternoonTemp / count($arItem['TEMP_AFTERNOON']);
                
                $nightTemp = 0;
                foreach ($arItem['TEMP_NIGHT'] as $value) {
                    $nightTemp += $value;
                }
                $nightTemp = $nightTemp / count($arItem['TEMP_NIGHT']);
                
                $arResult['ITEMS'][] = array(
                    FormatDate('d.m.Y', strtotime($dateString)),
                    $afternoonTemp,
                    $nightTemp
                );
            }
            unset($arForecast, $afternoonTemp, $nightTemp, $value, $dateString);
            break;
        
        // Информер
        default:
            foreach ($jsonResult['list'] as $arItem) {
                $timestamp = strtotime($arItem['dt_txt']);
                $arDate = array(
                    'DATE' => date('Y-m-d', $timestamp),
                    'HOUR' => date('G', $timestamp)
                );
                
                if (!isset($arResult['ITEMS'][$arDate['DATE']])) {
                    $arResult['ITEMS'][$arDate['DATE']] = array(
                        'TEMP_MIN' => $class->toTempC($arItem['main']['temp']),
                        'TEMP_MAX' => $class->toTempC($arItem['main']['temp']),
                        'DATE' => FormatDate(array('today' => 'today', 'tommorow' => 'tommorow', '' => 'j F Y'), $timestamp),
                        'WEATHER' => array()
                    );
                    
                    foreach ($arItem['weather'] as $arWeather) {
                        $arResult['ITEMS'][$arDate['DATE']]['WEATHER'][] = array(
                            'DESCRIPTION' => $arWeather['description'],
                            'ICON' => $class->getIcon($arWeather['icon'])
                        );
                    }
                }
                
                if ($class->toTempC($arItem['main']['temp']) < $arResult['ITEMS'][$arDate['DATE']]['TEMP_MIN']) {
                    $arResult['ITEMS'][$arDate['DATE']]['TEMP_MIN'] = $class->toTempC($arItem['main']['temp']);
                }
                
                if ($class->toTempC($arItem['main']['temp']) > $arResult['ITEMS'][$arDate['DATE']]['TEMP_MAX']) {
                    $arResult['ITEMS'][$arDate['DATE']]['TEMP_MAX'] = $class->toTempC($arItem['main']['temp']);
                }
                
                if ($class->isAfternoonTime($arItem['dt_txt'])) {
                    $arResult['ITEMS'][$arDate['DATE']]['WEATHER'] = array();
                    foreach ($arItem['weather'] as $arWeather) {
                        $arResult['ITEMS'][$arDate['DATE']]['WEATHER'][] = array(
                            'DESCRIPTION' => $arWeather['description'],
                            'ICON' => $class->getIcon($arWeather['icon'])
                        );
                    }
                }
            }
            unset($arWeather, $timestamp);
            break;
    }
    
    unset($arItem, $arDate, $jsonResult);
    
    $this->EndResultCache();
}

$arResult['CURRENT'] = array(
    'TEMP' => $class->toTempC($arResult['RESPONSE']['list'][0]['main']['temp']),
    'DESCRIPTION' => $arResult['RESPONSE']['list'][0]['weather'][0]['description'],
    'ICON' => $class->getIcon($arResult['RESPONSE']['list'][0]['weather'][0]['icon']),
    'DATETIME' => FormatDate('D, j F Y (H:i:s)', time() + $arResult['CITY']['TIMEZONE'] - date('Z'))
);

foreach ($arResult['RESPONSE']['list'] as $arItem) {
    if (time() <= strtotime($arItem['dt_txt'])) {
        break;
    }

    $arResult['CURRENT']['TEMP'] = $class->toTempC($arItem['main']['temp']);
    $arResult['CURRENT']['DESCRIPTION'] = $arItem['weather'][0]['description'];
    $arResult['CURRENT']['ICON'] = $class->getIcon($arItem['weather'][0]['icon']);
}

$this->IncludeComponentTemplate($componentPage);