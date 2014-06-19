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

function searchString(obj, dataType)
{
    obj.setAttribute("autocomplete", "off");
    var inputContent = obj.value;
    if (inputContent.search(',') > -1) {
        var elements = inputContent.split(',');
        var toSend   = elements.pop().replace(/^\s+/g,'');
    }
    else {
        var elements = [];
        var toSend = inputContent.replace(/^\s+/g,'');;
    };
    var http = createRequestObject();
    http.open('GET', '/admin.php?' + dataType + '=' + toSend, true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState === 4) {
            if (http.status === 200) {
                var result = eval( '(' + http.responseText + ')' );
                if (result !== false) {
                    if (document.getElementById(obj.id + 'suggest') === null) {
                        var ul            = document.createElement('ul');
                        ul.id             = obj.id + 'suggest';
                        ul.style.position = 'absolute';
                        ul.style.top      = eval(obj.offsetTop + obj.offsetHeight) + 'px';
                        var parent        = obj.parentNode;
                        parent.insertBefore(ul, obj);
                    };
                    document.getElementById(obj.id + 'suggest').innerHTML = '';
                    if (result['status'] === 'success') {
                        for(var i= 0; i < result.data.length; i++)
                        {
                            var li = document.createElement('li');
                            li.id  = obj.id + 'li' + i;
                            document.getElementById(obj.id + 'suggest').appendChild(li);
                            var a       = document.createElement('a');
                            a.innerHTML = result.data[i].value;
                            a.name      = obj.id + 'a' + i
                            a.onclick   = function() {
                                var parent  = this.parentNode;
                                // On concat la valeur cliquée au tableau de l'input
                                elements.push(this.innerHTML);
                                obj.value   = elements.join(',');
                                document.getElementById(obj.id + 'suggest').innerHTML = '';
                            };
                            document.getElementById(obj.id + 'li' + i).appendChild(a);
                        };
                        
                    };
                    /*obj.onkeyup = function(event) {
                        console.log(event.keyCode);
                    };*/
                };
            };
        };
    } );
    http.send(null);
}