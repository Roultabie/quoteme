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

function searchString(obj, dataType, event)
{
    var currentKey = event.keyCode;
    obj.setAttribute("autocomplete", "off");
    var inputContent = obj.value;
    //console.log(inputContent.search(/,$/g));
    if (inputContent.search(',') !== -1) {
        var elements = inputContent.split(',');
        var toSend   = elements.pop().replace(/^\s+/g,'');
        // Si on a une , c'est qu'on a un nouveau tag, donc on calcul la position des suggestions
        if (currentKey === 188 || currentKey === 8 && inputContent.search(/,$/g) != -1) {
            if (currentKey === 188) {
                calculateBubblePosition(obj, 0);
            }
            else {
                calculateBubblePosition(obj, 1);
            };
        };
    }
    else {
        var elements = [];
        var toSend   = inputContent.replace(/^\s+/g,'');
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
                            var a           = document.createElement('a');
                            a.innerHTML     = result.data[i].value;
                            a.name          = obj.id + 'a' + i
                            a.style.display = 'block';
                            a.onclick       = function() {
                                var parent  = this.parentNode;
                                // On concat la valeur cliquée au tableau de l'input
                                elements.push(this.innerHTML);
                                obj.value = elements.join(',');
                                document.getElementById(obj.id + 'suggest').innerHTML = '';
                                obj.focus();
                            };
                            document.getElementById(obj.id + 'li' + i).appendChild(a);
                        };
                        
                    };
                };
            };
        };
    } );
    http.send(null);
};

function calculateBubblePosition(obj, remove)
{
    var string = obj.value;
    if (remove === 1) {
        //console.log(string);
        var elements = string.replace(/,$/g,'').split(',');
        var toRemove = elements.pop();
        console.log(elements.length);
        if (elements.length > 0) {
            string = elements.join(',') + ',';
        }
        else {
            string = elements.join(',');
        };
    };
    // On crée une DIV temporaire
    var temp = document.createElement('div');
    temp.id  = 'tempsearchstring';
    temp.style.display = 'none';
    document.body.appendChild(temp);
    // On y insère un span avec les données de l'input dedans
    var tempSpan = document.createElement('span');
    tempSpan.id  = 'tempspan1';
    tempSpan.style.display = 'inline-block';
    tempSpan.innerHTML = string;
    document.body.appendChild(tempSpan);
    // On crée un span après ce premier
    var tempSpanEnd = document.createElement('span');
    tempSpanEnd.id  = 'tempspan2';
    document.body.appendChild(tempSpanEnd);
    // Enfin, on calcule la longueur entre les deux, on lui ajoute la position de l'input
    var ulLeftPos = eval(tempSpanEnd.offsetLeft - tempSpan.offsetLeft + obj.offsetLeft);
    var ul = document.getElementById(obj.id + 'suggest');
    // Puis on applique le résultat à la bulle de suggestion
    ul.style.left = ulLeftPos + 'px';
    // Pour finir on détruit la div et ce qu'elle contient.
    temp.parentNode.removeChild(temp); 
};