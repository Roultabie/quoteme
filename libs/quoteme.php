<?php
/**
 * Quoteme class
 *
 * Generate quote object
 *
 * @package     quoteme
 * @author      Daniel Douat <daniel.douat@aelys-info.fr>
 * @link        http://www.aelys-info.fr
 */

class quote
{
    private $text;
    private $author;
    private $source;

    function __construct()
    {
        //$this->setText('');
        //$this->setAuthor('');
        //$this->setSource('');
    }

    /**
     * Return quote text
     * @access public
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Return author(s) for quote
     * @access public
     * @return array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Return source of quote
     * @access public
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Add new quote
     * @access public
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Add author(s) for quote
     * @access public
     * @return void
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Add source(s) for quote
     * @access public
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }
}

class quoteQueries
{
    private         $elements;
    private         $toAdd;
    private         $toDelete;
    private         $toEdit;
    private static  $stack;
    private static  $table;
    public  static  $nbQuotes;
    

    function __construct()
    {
        self::$table    = 'quotes';
        $nbQuotes       = $this->countElements('count');
        self::$nbQuotes = $nbQuotes[0]->nb;
    }

    /**
     * [getQuote description]
     * @param  string  $option quote options like all, number, (number), nb1:nbX
     * @param  boolean $random to ramdomize quote list set to TRUE
     * @return array   $quote  one line by quote
     */
    public function getQuote($options = '')
    {
        $quotesList = $this->selElements($options);
        if (is_array($quotesList)) {
            $nbElements = count($quotesList);
            for ($i = 0; $i < $nbElements; $i++) {
                $quote[$i] = new quote();
                $quote[$i]->setText($quotesList[$i]->quote);
                $quote[$i]->setAuthor($quotesList[$i]->author);
                $quote[$i]->setSource($quotesList[$i]->source);
            }
            return $quote;
        }
    }

    /**
     * [addQuote description]
     * @param  string $text   quote text, can't be empty
     * @param  string $author author or empty
     * @param  string $source quote source or empty (ex, book, internet)
     * @return array  $result an array contains all quotes added
     */
    public function addQuote($text, $author = '', $source = '')
    {
        if (!empty($text)) {
            $result[] = array('quote' => $text, 'author' => $author, 'source' => $source);
        }
        return $result;
    }

    /**
     * [delQuote description]
     * @param  int   $id the sql id of quote
     * @return array $result an array contains deleted quote elements (key / values) 
     */
    public function delQuote($id) // si la quote est supprimée, on retourne celle-ci au cas ou on veuille revenir en arrière
    {
        if (is_int($id)) {
            $result[] = $id;
        }
        return $result;
    }

    /**
     * [editQuote description]
     * @param  int    $id     sql id of quote
     * @param  string $text   quote (can't be empty)
     * @param  string $author author or empty
     * @param  string $source source or empty
     * @return array          an array contains all quotes edited
     */
    public function editQuote($id, $text, $author = '', $source = '')
    {
        if (is_int($id)) {
            if (!empty($text)) {
                $result[$id] = array('quote' => $text, 'author' => $author, 'source' => $source);
            }
        }
        return $result;
    }

    public function execStack()
    {
        $stack = self::getStacking();
        if (is_array($stack)) {
            foreach ($stack as $array) {
                foreach ($array as $type => $elements) { // on ventille les différentes requetes
                    if ($type === "insert") {
                        $insert[] = $elements;    
                    }
                    elseif ($type === "update") {
                        $update[] = $elements;
                    }
                    elseif ($type === "delete") {
                        $delete[] = $elements;  
                    }
                }
            }
            // puis on les exécute 
            $this->addElements($insert);
            $this->editElements($update);
            $this->delElements($delete);
        }
    }

    // End # public functions -------------------------------------------------
        
    // Start # private functions ----------------------------------------------
     
    private function randomizeQuotes($quotesList)
    {
        if (is_array($quotesList)) {
            shuffle($quotesList);
        }
        return $quotesList;
    }
    
    private static function getStacking()
    {
        return self::$stack;
    }

    private static function stack($type, $elements) // type : select, insert, update, delete
    {
        if (!empty($type)) {
            self::$stack[] = array($type => $elements);
        }
    }

    private function selElements($opt = "")
    {
        // On contrôle si pas d'option afin de n'afficher qu'une citation aléatoire, c'est crade mais provisoire
        $ctrl = FALSE;
        if (is_array($opt)) {
            foreach ($opt as $value) {
                if (!empty($value)) $ctrl = TRUE;
            }
        }
        if ($ctrl === FALSE) $opt = array('sort' => 'random', 'limit' => 1);
        if (!empty($opt['where']) && !empty($opt['whereOpt'])) {
            $wOpt    = explode(',', $opt['whereOpt']);
            $wOpt[0] = str_replace('minus', '<', $wOpt[0]);
            $wOpt[0] = str_replace('plus', '>', $wOpt[0]);
            $wOpt[0] = str_replace('equal', '=', $wOpt[0]);
            if ($wOpt[0] === 'like') {
                $wOpt[0] = strtoupper($wOpt[0]);
                $wOpt[1] = '%' . $wOpt[1] . '%';
            }
            $where   = ' WHERE ' .$opt['where'] . ' ' .$wOpt[0] . ' "' . $wOpt[1] . '"';
            if (!empty($opt['and']) && !empty($opt['andOpt'])) {
                $aOpt    = explode(',', $opt['andOpt']);
                $aOpt[0] = str_replace('minus', '<', $aOpt[0]);
                $aOpt[0] = str_replace('plus', '>', $aOpt[0]);
                $aOpt[0] = str_replace('equal', '=', $aOpt[0]);
                if ($aOpt[0] === 'like') {
                    $aOpt[0] = strtoupper($wOpt[0]);
                    $aOpt[1] = '%' . $aOpt[1] . '%';
                }
                $where .= ' AND ' .$opt['and'] . ' ' .$aOpt[0] . ' "' . $aOpt[1] . '"';

            }
        }
        if (!empty($opt['limit'])) {
            if (strpos($opt['limit'], ',') !== FALSE) {
                $limit = explode(',', $opt['limit']);
                $limit[1] = ',' . $limit[1];
            }
            else {
                $limit[0] = $opt['limit'];
            }
            $limit = ' LIMIT ' . $limit[0] . $limit[1];
        }
        if (!empty($opt['sort'])) {
            if ($opt['sort'] == 'random') $rand = ' JOIN ( SELECT FLOOR( COUNT( * ) * RAND( ) ) AS ValeurAleatoire FROM ' . self::$table . ' ) AS V ON ' . self::$table . '.id >= V.ValeurAleatoire';
            if (strpos($opt['sort'], ',')) {
                $sOpt = explode(',', $opt['sort']);
                $sort = ' ORDER BY ' . $sOpt[0] . ' ' .$sOpt[1];
            }
        }
        $query = 'SELECT quote, author, source FROM ' . self::$table . $rand . $where . $sort . $limit . ';';
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    private function countElements()
    {
        $stmt = dbConnexion::getInstance()->prepare('SELECT COUNT(*) AS nb FROM ' . self::$table .';');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    private function delElements($elements) // $ids = quotes to del (array)
    {
        $stmt = dbConnexion::getInstance()->prepare('DELETE ' . self::$table .' WHERE id = :id;');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':id', $datas, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    private function addElements($elements) // array = quotes to add (array key[] = array key = fields, value = values)
    {
        $stmt = dbConnexion::getInstance()->prepare('INSERT INTO ' . self::$table .' (quote, author, source) VALUES (:quote, :author, :source);');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':quote', $datas['quote'], PDO::PARAM_STR);
                $stmt->bindValue(':author', $datas['author'], PDO::PARAM_STR);
                $stmt->bindValue(':source', $datas['source'], PDO::PARAM_STR);
                $stmt->execute();
            }

        }
    }

    private function editElements($elements) // array multidimentional = quotes to edit (array keys = ids of elements, array inside: key fields, values values)
    {
        $stmt = dbConnexion::getInstance()->prepare('UPDATE ' . self::$table .' SET quote = :quote, author = :author, source = :source WHERE id = :id');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':id', $datas['id'], PDO::PARAM_INT);
                $stmt->bindValue(':quote', $datas['quote'], PDO::PARAM_STR);
                $stmt->bindValue(':author', $datas['author'], PDO::PARAM_STR);
                $stmt->bindValue(':source', $datas['source'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }
}