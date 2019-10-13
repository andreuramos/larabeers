@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card mb-3">
                    <div class="card-header">
                        Map
                    </div>
                    <div class="card-body">
                        <div id="regions_div"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Countries
                    </div>
                    <div class="card-body">
                        <div id="chart_div" style="height: 1300px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages':['geochart', 'corechart', 'bar'],
            // Note: you will need to get a mapsApiKey for your project.
            // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
            'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
        });
        google.charts.setOnLoadCallback(drawBarChart); //drawRegionsMap
        google.charts.setOnLoadCallback(drawRegionsMap); //

        function drawRegionsMap() {
            var data = google.visualization.arrayToDataTable([
                ['Country', 'Beers'],
                @foreach($countries as $country)
                    [ '{{$country['name']}}', {{$country['beers']}} ],
                @endforeach
            ]);
            var options = {
                backgroundColor: {
                    fill: '#FFFFFF'
                },
                colorAxis:{
                    minValue: 0,
                    colors: ['#CCCCFF','#2222FF']
                }
            };
            var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
            chart.draw(data, options);
        }
        function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                ['Country', 'Beers'],
                    @foreach($countries as $country)
                        [ '{{$country['name']}}', {{$country['beers']}} ],
                    @endforeach
            ]);
            var options = {
                title: 'Beer origin',
                chartArea: {width: '50%'},
                hAxis: {
                    title: 'Beers',
                    minValue: 0
                },
                vAxis: {
                    title: 'Country'
                },
                chartArea: {
                    height: 1300
                }
            };
            var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>

@endsection
