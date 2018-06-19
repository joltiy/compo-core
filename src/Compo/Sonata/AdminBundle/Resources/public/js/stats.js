function createChartStock(tableCharts, id, column_id, dimensions_size, timeline) {
    var columns = [];

    //console.log(column_id);
    //console.log(dimensions_size);


    $('thead th', tableCharts).each(function () {
        var th = $(this);

        var item = {};

        item.type = th.data('type');
        item.filed_type = th.data('field-type');
        item.label = th.text().trim();

        columns.push(item);
    });


    var series_obj = {};
    var total_data = 0;

    if (timeline) {
        total_data = {};
    }

    /*
    var series_name = '';

    if (timeline && dimensions_size == 2) {
        series_name = $('th:nth-child(2)', tableCharts).text().trim();
    } else {
        for (var i = 2; i <= dimensions_size; i++) {
            series_name = series_name + ' ' + $('th:nth-child(' + i + ')', tableCharts).text().trim();
        }

        series_name = series_name.trim();
    }
    */

    $('tbody tr', tableCharts).each(function () {
        //var item = [];

        var tr = $(this);

        if (timeline) {
            var date = parseInt($('td:nth-child(1)', tr).data('raw')) * 1000;
        }

        var series_name = '';

        if (timeline && parseInt(dimensions_size) === 2) {
            series_name = $('td:nth-child(2)', tr).text().trim();
        } else {
            for (var i = 2; i <= dimensions_size; i++) {
                series_name = series_name + ' ' + $('td:nth-child(' + i + ')', tr).text().trim();
            }

            series_name = series_name.trim();
        }

        var value_str = $('td:nth-child(' + column_id + ')', tr).text().trim();

        var value = parseInt(value_str);


        if (series_obj[series_name] === undefined) {
            series_obj[series_name] = {
                'name': series_name,
                'data': [],
                'connectNulls': true,
                'connectEnds': true
            };
        }

        if (timeline) {
            series_obj[series_name].data.push([date, value]);

            if (total_data[date] === undefined) {
                total_data[date] = {
                    'date': date,
                    'value': 0
                };
            }

            total_data[date].date = date;
            total_data[date].value += value;

        } else {
            series_obj[series_name].data.push([value]);

            total_data += value;
        }

    });

    if (timeline) {
        var total_data_array = [];

        $.each(total_data, function (index, value) {

            total_data_array.push([value.date, value.value]);
        });


        series_obj['Всего'] = {
            'name': 'Всего',
            'data': total_data_array,
            'connectNulls': true,
            'connectEnds': true
        };
    } else {
        series_obj['Всего'] = {
            'name': 'Всего',
            'data': [total_data],
            'connectNulls': true,
            'connectEnds': true
        };
    }


    var series = [];

    $.each(series_obj, function (index, value) {
        if (timeline) {
            var unordered = {};

            $.each(value.data, function (index_row, value_row) {
                unordered[value_row[0]] = value_row;
            });

            value.data = [];


            Object.keys(unordered).sort().forEach(function(key) {
                value.data.push(unordered[key]);
            });

            series.push(value);

        } else {
            series.push(value);
        }
    });


    Highcharts.stockChart('chart-' + id, {
        chart: {
            type: 'spline'
        },
        rangeSelector: {
            inputDateFormat: '%d.%m.%Y',
            inputEditDateFormat: '%d.%m.%Y',

            buttonTheme: {
                width: 50
            },
            allButtonsEnabled: true,
            buttons: [
                {
                    type: 'week',
                    count: 1,
                    text: 'День',
                    dataGrouping: {
                        forced: true,
                        units: [['day', [1]]]
                    }
                },
                {
                    type: 'month',
                    count: 4,
                    text: 'Неделя',
                    dataGrouping: {
                        forced: true,
                        units: [['week', [1]]]
                    }
                },
                {
                    type: 'year',
                    count: 12,
                    text: 'Месяц',
                    dataGrouping: {
                        forced: true,
                        units: [['month', [1]]]
                    }
                }
            ],
            selected: 0
        },
        title: {
            text: ''
        },

        xAxis: {
            type: 'datetime'
        },

        yAxis: {
            title: {
                text: $('th:nth-child(' + column_id + ')', tableCharts).text().trim()
            }
        },

        legend: {
            enabled: true,
            align: 'right',
            backgroundColor: '#FCFFC5',
            borderColor: 'black',
            borderWidth: 2,
            layout: 'vertical',
            verticalAlign: 'middle',
            shadow: true

        },

        plotOptions: {
            series: {
                dataGrouping: {
                    approximation: 'sum'
                },
                allowPointSelect: true,
                label: {
                    connectorAllowed: false
                },

                showInNavigator: true,
                connectNulls: true,

                line: {
                    connectNulls: true
                },
                spline: {
                    connectNulls: true
                }
            },
            line: {
                connectNulls: true
            },
            spline: {
                connectNulls: true
            }
        },

        series: series,
        tooltip: {
            shared: false,
            crosshairs: false,
            split: true

        },
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    }, function (chart) {
        setTimeout(function () {
            $('input.highcharts-range-selector', $(chart.container).parent())
                .datepicker({
                    regional: $.datepicker.regional['ru'],
                    dateFormat: 'dd.mm.yy',
                    onSelect: function () {
                        this.onchange();
                        this.onblur();
                    }
                });
        }, 0);
    });
}

function createChart(tableCharts, id, column_id, dimensions_size, ignore) {
    var columns = [];

    if (ignore === undefined) {
        ignore = [];
    }

    $('thead th', tableCharts).each(function () {
        var th = $(this);

        var item = {};

        item.type = th.data('type');
        item.filed_type = th.data('field-type');
        item.label = th.text().trim();

        columns.push(item);
    });

    //var series_obj = {};

    var yAxis_label = $('th:nth-child(' + column_id + ')', tableCharts).text().trim();

    var xAxis_label = $('th:nth-child(' + 1 + ')', tableCharts).text().trim();


    var categories = {};

    var series_columns = {};
    var series_data = {};

    $('tbody tr', tableCharts).each(function () {
        var tr = $(this);

        if (!ignore.indexOf($('td:nth-child(1)', tr).text().trim())) {
            return;
        }

        categories[$('td:nth-child(1)', tr).text().trim()] = $('td:nth-child(1)', tr).text().trim();

        var value_str = $('td:nth-child(' + column_id + ')', tr).text().trim();

        var value = parseInt(value_str);

        if (dimensions_size > 1) {
            series_columns[$('td:nth-child(1)', tr).text().trim()] = $('td:nth-child(1)', tr).text().trim();
        }


        if (dimensions_size > 1) {
            if (series_data[$('td:nth-child(2)', tr).text().trim()] === undefined) {
                series_data[$('td:nth-child(2)', tr).text().trim()] = {};
            }
        } else {
            if (series_data[$('td:nth-child(1)', tr).text().trim()] === undefined) {
                series_data[$('td:nth-child(1)', tr).text().trim()] = {};
            }
        }

        if (dimensions_size > 1) {
            series_data[$('td:nth-child(2)', tr).text().trim()][$('td:nth-child(1)', tr).text().trim()] = value;
        } else {
            series_data[$('td:nth-child(1)', tr).text().trim()] = value;
        }
    });


    var series = [];

    if (dimensions_size > 1) {


        $.each(series_data, function (index, value) {

            var series_item = {
                name: yAxis_label,
                data: []
            };
            series_item.name = index;

            $.each(series_columns, function (series_columns_index) {
                if (value[series_columns_index] === undefined) {
                    series_item.data.push(0);
                } else {
                    series_item.data.push(value[series_columns_index]);
                }
            });

            series.push(series_item);

        });

    } else {
        var data = [];

        $.each(series_data, function (index, value) {
            data.push(value);
        });

        series.push({
            name: yAxis_label,

            type: 'column',
            colorByPoint: true,
            showInLegend: false,
            data: data
        });
    }


    var categories_array = [];

    $.each(categories, function (index, value) {
        categories_array.push(value);
    });


    Highcharts.chart('chart-' + id, {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: categories_array,
            crosshair: true,
            title: {
                text: xAxis_label
            }
        },

        yAxis: {
            title: {
                text: yAxis_label
            }
        },

        legend: {
            enabled: true,
            align: 'right',
            backgroundColor: '#FCFFC5',
            borderColor: 'black',
            borderWidth: 2,
            layout: 'vertical',
            verticalAlign: 'middle',
            shadow: true
        },

        plotOptions: {
            series: {
                showInNavigator: true,
                connectNulls: true
            }
        },

        series: series
    });






}

function setupStatsDimensions(subject) {
    $('.form-stats-entity', subject).each(function () {
        var entity = $(this);

        entity.change(function () {
            // ... retrieve the corresponding form.
            var $form = $(this).closest('form');
            // Simulate form data, but only include the selected sport value.
            var data = {};
            data[entity.attr('name')] = entity.val();
            // Submit data via AJAX to the form's action path.

            data['get_dimensions'] = 1;
            data['entity'] = entity.val();

            $.ajax({
                url : $form.attr('action'),
                type: 'GET',
                data : data,
                success: function(html) {
                    // Replace current position field ...
                    $('.form-stats-dimensions', $form).parent().html(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.form-stats-dimensions').parent().html()
                    );

                    $('.form-stats-metrics', $form).parent().html(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.form-stats-metrics').parent().html()
                    );

                    //Admin.setup_collection_counter(subject);

                    //Admin.setup_collection_buttons(subject);

                    // Position field now displays the appropriate positions.
                }
            });
        });
    });
}

$(function ($) {
    $(window).on("compo.sonata.admin.shared_setup", function( event, data ) {
        Admin.log('[compo|stats] on', data.subject);

        setupStatsDimensions(data.subject);
    });
});
