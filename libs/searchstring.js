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

function searchString(obj, dataType, event, maxKeywords = 'n')
{
    obj.onblur = function() {
        removeBubble(this);
    };
    obj.setAttribute("autocomplete", "off");
    var currentKey   = event.keyCode;
    var comma        = ',';
    var inputContent = obj.value;
    var elements     = inputContent.split(',');
    // nombre de mot complets (si pas de virgule le mot n'est potentiellement pas terminé)
    var nbElements   = elements.length - 1;
    if (inputContent === '') {
        nbElements   = 0;
    };
    // Si il y a autant de mots clés que la limite, on bloque l'ajout de la virgule
    if (maxKeywords === nbElements) {
        obj.value = inputContent.replace(/,{1,}/g, '');
        return false
    }
    obj.removeAttribute('maxLength');
    if (nbElements > 0) {
        // Si on trouve au moins deux fois une virgule, on la remplace par une seule
        if (inputContent.search('/,{2,}/g')) {
            obj.value = inputContent.replace(/,{2,}/g, ',');
        };
        var toSend = elements.pop().replace(/^\s+/g,'');
        // On surveille si on tape une virgule
        if (currentKey === 188) {
            calculateBubblePosition(obj, 0);
        }
        else {
            calculateBubblePosition(obj, 1);
        };
    }
    else {
        var elements = [];
        var toSend   = inputContent.replace(/^\s+/g,'');
        // Si on efface tout via sélection complète de l'input, on réinitialise la suggestion
        if (document.getElementById(obj.id + 'suggest') !== null) {
            calculateBubblePosition(obj, "all");
        };
        
    };
    if (currentKey === 38 || currentKey === 40) {
        moveFocus(obj, currentKey);
        return false;
    };
    // Si on tape sur la touche entrée on ajoute le tag en "focus"
    if (currentKey === 9 || currentKey === 13) {
        var focused = document.getElementsByClassName(obj.id + '-suggest-focus')[0].childNodes[0];
        elements.push(focused.innerHTML);
        obj.value = elements.join(',');
        toSend = '';
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
                        obj.parentNode.insertBefore(ul, obj);
                    };
                    
                    document.getElementById(obj.id + 'suggest').innerHTML = '';
                    if (result['status'] === 'success') {
                        for(var i= 0; i < result.data.length; i++)
                        {
                            var li          = document.createElement('li');
                            li.id           = obj.id + 'li' + i;
                            li.onmouseover  = function() {
                                document.getElementsByClassName(obj.id + '-suggest-focus')[0].className = '';
                            };
                            li.onmouseout   = function() {
                                this.className = obj.id + '-suggest-focus';
                            };
                            document.getElementById(obj.id + 'suggest').appendChild(li);
                            var a           = document.createElement('a');
                            a.innerHTML     = result.data[i].value;
                            a.id            = obj.id + 'a' + i;
                            a.name          = obj.id + 'a'
                            a.style.display = 'block';
                            a.href          = '#';
                            // Quand on détecte que le clic, on désactive le removeBubble sinon le clic n'est pas pris en compte
                            a.onmousedown   = function() {
                                obj.onblur  = '';
                            }
                            // Puis une fois cliqué on ajoute le mot à l'input
                            a.onclick        = function() {
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
        var elements = string.replace(/,$/g,'').split(',');
        var toRemove = elements.pop();
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
    document.body.appendChild(temp);
    // On y insère un span avec les données de l'input dedans
    var tempSpan           = document.createElement('span');
    tempSpan.id            = 'tempspan1';
    tempSpan.style.display = 'inline-block';
    tempSpan.innerHTML     = string;
    temp.appendChild(tempSpan);
    // On crée un span après ce premier
    var tempSpanEnd = document.createElement('span');
    tempSpanEnd.id  = 'tempspan2';
    temp.appendChild(tempSpanEnd);
    // Enfin, on calcule la longueur entre les deux, on lui ajoute la position de l'input
    var ulLeftPos = eval(tempSpanEnd.offsetLeft - tempSpan.offsetLeft + obj.offsetLeft);
    // Si la bulle ne s'est pas créée, on le fait.
    if (document.getElementById(obj.id + 'suggest') === null) {
        var ul            = document.createElement('ul');
        ul.id             = obj.id + 'suggest';
        ul.style.position = 'absolute';
        ul.style.top      = eval(obj.offsetTop + obj.offsetHeight) + 'px';
        obj.parentNode.insertBefore(ul, obj);
    };
    // Puis on applique le résultat à la bulle de suggestion
    if (document.getElementById(obj.id + 'suggest') !== null) {
        var ul = document.getElementById(obj.id + 'suggest');
        if (remove === "all") {
            ul.style.left = obj.offsetLeft + 'px';
        }
        else {
            ul.style.left = ulLeftPos + 'px';
        };
    };
    // Pour finir on détruit la div et ce qu'elle contient.
    document.body.removeChild(temp); 
};

function moveFocus(obj, key)
{
    if (document.getElementById(obj.id + 'suggest') !== null) {
        var ul = document.getElementById(obj.id + 'suggest');
        var li = ul.childNodes;
        for (var i = 0; i < li.length; ++i) {
            var currentClass = li[i].className;
            if (currentClass === obj.id + '-suggest-focus') {
                if (key === 38) {
                    var nextLi = i - 1;
                };
                if (key === 40) {
                    var nextLi = i + 1;
                };
                li[i].className = '';
                break;
            }
            else {
                if (key === 38) {
                    var nextLi = li.length - 1;
                };
                if (key === 40) {
                    var nextLi = 0;
                };
            };
        };
        if (nextLi > li.length - 1) {
            nextLi = 0;
        };
        if (nextLi < 0) {
            nextLi = li.length - 1;
        };
        var nextLiId = obj.id + 'li' + nextLi;
        document.getElementById(nextLiId).className = obj.id + '-suggest-focus';
    };
}

function bypassNavigation(obj, event)
{
    if (event.keyCode === 9 || event.keyCode === 13) {
        if (document.getElementById(obj.id + 'suggest') !== null && document.getElementById(obj.id + 'suggest').innerHTML !== '') {
            if (event.preventDefault) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                event.returnValue  = false;
                event.cancelBubble = true;
            };
        };
    };
}

function removeBubble(obj, event)
{
    if (document.getElementById(obj.id + 'suggest') !== null) {
        document.getElementById(obj.id + 'suggest').parentNode.removeChild(document.getElementById(obj.id + 'suggest'));
    };
}