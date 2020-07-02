<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die(http_response_code(403));

    if (!empty($arResult['ITEMS'])):
?>
  <div class="chart-sd chart-temperature">
    <div class="chart-sd__title"><?=GetMessage('CHART_TITLE');?></div>
    <div class="chart-sd__area">
      <div class="chart-sd__graph">
        <div id="chart-temperature-sd" style="width: 100%; height: 225px;"></div>
      </div>
    </div>
  </div>

  <?php $jsData = json_encode(array_merge(array(array('', GetMessage("CHART_LABEL_TEMP_DAY"), GetMessage("CHART_LABEL_TEMP_NIGHT"))), $arResult['ITEMS'])); ?>
  <script src="https://www.google.com/jsapi"></script>
  <script>
  createGoogleChart(
    'chart-temperature-sd',
    <?=$jsData;?>,
    {
      title: '',
      legend: { position: 'bottom' },
      vAxis: { title: '<?=GetMessage("CHART_LABEL_V_AXIS");?>'},
      series: {
        0: { color: '#ffb300' },
        1: { color: '#673ab7' }
      }
    }
  );
  </script>
<?php endif; ?>