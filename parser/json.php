<?php
class jsonParser
extends parser
implements parserTemplate
{
    public function parse($elements)
    {
        $i = 0;
        if (is_array($elements)) {
            foreach ($elements as $value) {
                $quotes[$i]['text']   = $value->getText();
                $quotes[$i]['author'] = $value->getAuthor();
                $quotes[$i]['source'] = $value->getSource();
                $i++;
            }
            $datas['status'] = 'success' ;
            $datas['data']   = $quotes;
        }
        else {
            $datas['status'] = 'error';
            $datas['data']   = 'Error, no data found';
        }
        $result = json_encode($datas);
        header('Cache-Control: no-cache, must-revalidate'); // Gestion du cache et optimisations
        header('Expires: Ven, 11 Oct 2011 23:32:00 GMT');   // Limite la durée validité
        header('Content-type: application/json');           // Über _IMPORTANT_
        exit($result);
    }
}
?>