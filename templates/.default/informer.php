<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die(http_response_code(403));
?>
<div class="informer-sd">
  <div class="informer-sd__head">
    <div class="informer-sd__city"><?=$arResult['CITY']['NAME'];?></div>
    <div class="informer-sd__current"><?=$arResult['CURRENT']['DATETIME'];?></div>
  </div>
  <div class="informer-sd__weather">
    <span class="informer-sd__forecast">
      <span class="informer-sd__temperature"><?=$arResult['CURRENT']['TEMP'];?>&deg;C</span>
      <span class="informer-sd__visibility"><?=$arResult['CURRENT']['DESCRIPTION'];?></span>
    </span>
    <span class="informer-sd__icon"><img src="<?=$arResult['CURRENT']['ICON'];?>" alt=""></span>
  </div>
  <div class="informer-sd__list">
    <?php foreach ($arResult['ITEMS'] as $arItem): ?>
    <div class="informer-sd__item">
      <span class="informer-sd__item-date"><?=$arItem['DATE'];?></span>
      <span class="informer-sd__item-icon"><img src="<?=$arItem['WEATHER'][0]['ICON'];?>" alt=""></span>
      <?php if ($arItem['TEMP_MAX'] > $arItem['TEMP_MIN']) { ?>
      <span class="informer-sd__item-temperature"><?=$arItem['TEMP_MIN'];?>&deg; ... <?=$arItem['TEMP_MAX'];?>&deg;</span>
      <?php } else { ?>
      <span class="informer-sd__item-temperature"><?=$arItem['TEMP_MAX'];?>&deg;</span>
      <?php } ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>