function createGoogleChart(nodeId, data = [], options = {})
{
  var nodeId, data, options;

  google.load('visualization', '1', {
    packages: ['corechart']
  });
  
  google.setOnLoadCallback(function() {
    data = google.visualization.arrayToDataTable(data);
    var chart = new google.visualization.ColumnChart(document.getElementById(nodeId));
    chart.draw(data, options);
  });
}