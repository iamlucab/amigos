@extends('adminlte::page')
@section('title', 'Genealogy Tree')
@section('content_header')
    <h5>My Network</h5>
@stop

@section('content')
    <div id="chart_div"></div>
@stop

@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');

        data.addRows([
           @foreach($chartData as $row)
    [ 
        { v: '{{ $row[0]['v'] }}', f: `{!! $row[0]['f'] !!}` }, 
        '{{ $row[1] }}' 
    ],
@endforeach
        ]);

        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml:true});
      }
    </script>
@stop

@include('partials.mobile-footer')