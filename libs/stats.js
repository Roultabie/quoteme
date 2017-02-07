function createRequestObject()
{
    var http;
    if (window.XMLHttpRequest) { // Mozilla, Konqueror/Safari, IE7 ...
        http = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) { // Internet Explorer 6
        http = new ActiveXObject("Microsoft.XMLHTTP");
    }
    return http;
}

function getDelivered(chart, user, source)
{
    var http = createRequestObject();
    var uri = (user !== undefined) ? '&user=' + user + '&sortby=' + source : '';
    http.open('GET', '/admin/api/api.php?function=stats&type=delivered' + uri, true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState === 4) {
            if (http.status === 200) {
                var result = eval( '(' + http.responseText + ')' );
                var datas = result.data;
                if (datas['code'] === 200) {
                    var labels = [];
                    var series = [];
                    if (datas.items.length > 1 && user === undefined) {
                        datas.items.pop();
                    }
                    for(var i= 0; i < datas.items.length; i++)
                    {
                        if (user === undefined) {
                            labels.push(datas.items[i]['username'] + '(' + datas.items[i]['count'] + ')');
                        }
                        else if (source === 'network') {
                            labels.push(datas.items[i]['source'] + '(' + datas.items[i]['count'] + ')');
                        }
                        else if (source === 'permalink') {
                            labels.push(datas.items[i]['permalink'] + '(' + datas.items[i]['count'] + ')');
                        }
                        series.push(datas.items[i]['count']);
                    };
                    var data = {
                        'labels': labels,
                        'series': series
                    };
                    var options = {
                        labelInterpolationFnc: function(value) {
                          return value[0]
                        }
                     };

                    var responsiveOptions = [
                        ['screen and (min-width: 640px)', {
                            chartPadding: 30,
                            labelOffset: 100,
                            labelDirection: 'explode',
                            labelInterpolationFnc: function(value) {
                                return value;
                            }
                        }],
                        ['screen and (min-width: 1024px)', {
                            labelOffset: 50,
                            chartPadding: 20,
                            labelColor: '#000'
                        }]
                    ];

                    new Chartist.Pie(chart, data, options, responsiveOptions);
                };
            };
        };
    } );
    http.send(null);
};

function getPosted()
{
    var http = createRequestObject();
    http.open('GET', '/admin/api/api.php?function=stats&type=posted', true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState === 4) {
            if (http.status === 200) {
                var result = eval( '(' + http.responseText + ')' );
                var datas = result.data;
                if (datas['code'] === 200) {
                    var labels = [];
                    var series = [];
                    if (datas.items.length > 1) {
                        datas.items.pop();
                    }
                    for(var i= 0; i < datas.items.length; i++)
                    {
                        labels.push(datas.items[i]['username'] + '(' + datas.items[i]['count'] + ')');
                        series.push(datas.items[i]['count']);
                    };
                    var data = {
                        'labels': labels,
                        'series': series
                    };
                    var options = {
                        labelInterpolationFnc: function(value) {
                          return value[0]
                        }
                     };

                    var responsiveOptions = [
                        ['screen and (min-width: 640px)', {
                            chartPadding: 30,
                            labelOffset: 100,
                            labelDirection: 'explode',
                            labelInterpolationFnc: function(value) {
                                return value;
                            }
                        }],
                        ['screen and (min-width: 1024px)', {
                            labelOffset: 50,
                            chartPadding: 20,
                            labelColor: '#000'
                        }]
                    ];

                    new Chartist.Pie('#chartPosted', data, options, responsiveOptions);
                };
            };
        };
    } );
    http.send(null);
};

function chartsForUser()
{
    var http = createRequestObject();
    http.open('GET', '/admin/api/api.php?getuserinfos', true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState === 4) {
            if (http.status === 200) {
                var result = eval( '(' + http.responseText + ')' );
                var userInfos = result.data['items'];
                getDelivered('#chartDeliveredBySource', userInfos['id'], 'network');
                getDelivered('#chartDeliveredByQuote', userInfos['id'], 'permalink');
            };
        };
    } );
    http.send(null);
};
chartsForUser();
getDelivered('#chartDelivered');
getPosted();
