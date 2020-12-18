@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card mb-3">
                    <div class="card-header">
                        Beers included by year
                    </div>
                    <div class="card-body">
                        <div id="years_div" style="max-height: 100%; height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages':['corechart'],
            // Note: you will need to get a mapsApiKey for your project.
            // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
            'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
        });
        google.charts.setOnLoadCallback(drawBarChart);

        function drawBarChart() {
            $.ajax({
                url: '/api/count-by-year',
                success: function (json_data) {
                    let rows = [ ['Year', 'Beers'] ];
                    for (year in json_data) {
                        rows.push([year, parseInt(json_data[year])])
                    }

                    var data = google.visualization.arrayToDataTable(rows);
                    var options = {
                        title: 'Beers included every year',
                        hAxis: {
                            minValue: 0
                        },
                        legend: {
                            position: 'none'
                        }
                    };
                    var chart = new google.visualization.ColumnChart(document.getElementById('years_div'));
                    chart.draw(data, options);
                }
            })

        }
    </script>

@endsection
