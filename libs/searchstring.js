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

function searchString(obj)
{
    var http = createRequestObject();
    http.open('GET', '/admin.php?tag=' + obj.value, true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState === 4) {
            if (http.status === 200) {
                var result = eval( '(' + http.responseText + ')' );
                if (result !== false) {
                    if (document.getElementById(obj.id + 'choice') === null) {
                        var div            = document.createElement('div');
                        div.id             = obj.id + 'choice';
                        div.style.position = 'fixed';
                        div.style.top      = eval(obj.offsetTop + obj.offsetHeight);
                        var parent         = obj.parentNode;
                        parent.insertBefore(div, obj);
                    };
                    document.getElementById(obj.id + 'choice').innerHTML = '';
                    if (result['status'] === 'success') {
                        for(var i= 0; i < result.data.length; i++)
                        {
                            var span            = document.createElement('span');
                            span.innerHTML      = result.data[i].value;
                            span.dataset.origin = obj.id;
                            span.onclick        = function() {
                                setOrigin(span);
                            };
                            
                            document.getElementById(obj.id + 'choice').appendChild(span);
                        };
                        
                    };
                };
            };
        };
    } );
    http.send(null);
}

function setOrigin(obj)
{
    var origin  = obj.dataset.origin;
    var input   = document.getElementById(origin);
    var parent  = obj.parentNode;
    input.value = obj.innerHTML;
    document.getElementById(parent.id).innerHTML = '';
}