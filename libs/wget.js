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

function changeQuote()
{
    var http = createRequestObject();
    http.open('GET', 'api.php?p=json', true);
    http.onreadystatechange = ( function ()
    {
        if (http.readyState == 4) {
            if (http.status == 200) {
                var quote = eval( '(' + http.responseText + ')' );
                if (quote !== false) {
                    var oldTitle                                  = document.title;
                    var oldAuthor                                 = document.getElementById('author').innerHTML;
                    var oldAuthorSearchLink                       = document.getElementById('author').href;
                    var titleParts                                = oldTitle.split('-');
                    document.title                                = quote.data[0]['author'] + ' -' + titleParts.pop();
                    document.getElementById('textLink').innerHTML = quote.data[0]['text'];
                    document.getElementById('author').innerHTML   = quote.data[0]['author'];
                    document.getElementById('author').href        = oldAuthorSearchLink.replace(encodeURI(oldAuthor), quote.data[0]['author']);
                    document.getElementById('textLink').href      = '?' + quote.data[0]['permalink'];
                }
            }
        }
    } );
    http.send(null);
}