<?php
/**
 * Запрет на прямой просмотр файла
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die(http_response_code(403));
}

/**
 * WeatherInCityComponent class
 *
 * @author Алексей Шаповалов
 * @version 1.0
 */
final class WeatherInCityComponent
{
    /** * @const BASE_URL */
    const BASE_URL = 'https://api.openweathermap.org/data/2.5/forecast';
    
    /** * @var object $instance */
    private static $instance;
    
    /** * @var string $apiKey */
    private $apiKey;
    
    /** * @var string $cityName */
    private $cityName;
    
    /** * @var string $url */
    private $url;
    
    /**
     * Запрос к API Openweathermap по названию города
     *
     * @param string $cityName
     * @return false|array
     */
    public function request($cityName)
    {
        $this->cityName = $cityName;
        
        $this->buildUrl(array(
            'q' => $this->cityName,
            'appid' => $this->apiKey,
            'lang' => LANGUAGE_ID
        ));
        
        $httpClient = new \Bitrix\Main\Web\HttpClient();
        $httpClient->query('GET', $this->url);
        
        $httpResult = json_decode($httpClient->getResult(), true);
        
        return $httpResult['cod'] == 200 ? $httpResult : false;
    }
    
    /**
     * Формирование url запроса
     *
     * @param array $params
     */
    private function buildUrl($arParams = [])
    {
        $this->url = self::BASE_URL . '?' . http_build_query($arParams);
    }
    
    /**
     * Singletone создание объекта
     *
     * @param string $apiKey
     * @return object
     */
    public static function getInstance($apiKey)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($apiKey);
        }
        
        return self::$instance;
    }
    
    /**
     * Перевод из Кельвинов в градусы Цельсия
     *
     * @param numeric $K
     * @return numeric
     */
    public function toTempC($K)
    {
        return round(((float)$K - 274.15), 0);
    }
    
    /**
     * Получение url иконки
     *
     * @param string $name
     * @return string
     */
    public function getIcon($name)
    {
        return is_string($name) ? 'https://openweathermap.org/img/wn/'.$name.'.png' : '';
    }
    
    /**
     * Является ли переданное время ночным
     *
     * @param string $datetime
     * @return bool
     */
    public function isNightTime($datetime)
    {
        $hour = date('G', strtotime($datetime));
        return ($hour >= 0 && $hour < 4);
    }
    
    /**
     * Является ли переданное время утренним
     *
     * @param string $datetime
     * @return bool
     */
    public function isMorningTime($datetime)
    {
        $hour = date('G', strtotime($datetime));
        return ($hour >= 4 && $hour < 12);
    }
    
    /**
     * Является ли переданное время дневным
     *
     * @param string $datetime
     * @return bool
     */
    public function isAfternoonTime($datetime)
    {
        $hour = date('G', strtotime($datetime));
        return ($hour >= 12 && $hour < 17);
    }

    /**
     * Является ли переданное время вечерним
     *
     * @param string $datetime
     * @return bool
     */
    public function isEveningTime($datetime)
    {
        $hour = date('G', strtotime($datetime));
        return ($hour >= 17 && $hour < 24);
    }
        
    /**
     * Конструктор
     *
     * @return void
     */
    private function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
}