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
    private $elements;
    private $toAdd;
    private $toDelete;
    private $toEdit;
    private static $stack;
    public static $nbQuotes;

    function __construct()
    {
        $quotesIds = $this->selElements('all', 'id');
        self::$nbQuotes = count($quotesIds);
    }

    /**
     * [getQuote description]
     * @param  string  $option quote options like all, number, (number), nb1:nbX
     * @param  boolean $random to ramdomize quote list set to TRUE
     * @return array   $quote  one line by quote
     */
    public function getQuote($option = '', $random = FALSE, $createObj = TRUE) // options : all, number: for one, (number): for quantity, nb1;nb2;nbX: for multiple,   random, multi: all,random or 10,random, or (57), random or 1;5,18,39,radom
    {
        if ($option === "all") { // get all quotes
            $quotesList = $this->selElements('all');
        }
        elseif (is_int($option)) { // get specific quote
            $quotesList = $this->selElements('one', $option);
        }
        elseif ($option[0] === "(") { // get multiple quotes
            $quantity = trim($option, '()');
            $quotesList = $this->selElements('limit', $quantity);
        }
        elseif (strpos($option, ';') !== FALSE) { // get multi specific quotes
            $ids = trim(trim(str_replace(';', ',', str_replace(' ', '', $option)), ',')); // convert (10;48; 92;) to 10,48,92
            $quotesList = $this->selElements('multi', $ids);
        }
        elseif (empty($option)) { // get randomized quote
            // test de performances random
            $quotesList = $this->getQuote('all', TRUE, FALSE); // version 1 : Temps d'exécution (script total) : 0.7875189781189 secondes pour 1000 itérations
                
            /*$myQuotes = $this->selElements('all', 'id', FALSE); // version 2 : Temps d'exécution (script total) : 0.89189100265503 secondes pour 1000 itérations
            if (is_array($myQuotes)) {
                $id = array_rand($myQuotes, 1);
                $quotesList = $this->getQuote($id, FALSE, FALSE);
            }*/
        }
        if ($random === TRUE) { // randomize quotes / get randomized quotes
            $quotesList = $this->randomizeQuotes($quotesList);
        }
        if ($createObj) {
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
        else {
            return $quotesList;
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

    private function selElements($option, $fields = "")
    {
        if ($option === "all") {
            $stmt = dbConnexion::getInstance()->prepare('SELECT quote, author, source FROM quotes;');
            $stmt->execute();
        }
        elseif ($option === "one") {
            $stmt = dbConnexion::getInstance()->prepare('SELECT quote, author, source FROM quotes WHERE id=:id;');
            $stmt->bindValue(':id', $fields, PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif ($option === "multi") {
            $idsPH = preg_replace('/\d+/', '?', $fields);
            $ids   = explode(',', $fields);
            $nbIds = count($ids);
            $stmt  = dbConnexion::getInstance()->prepare('SELECT quote, author, source FROM quotes WHERE id IN(' .$idsPH . ');');
            for ($i = 0; $i < $nbIds; $i++) { 
                 $stmt->bindValue($i+1, $ids[$i], PDO::PARAM_INT);
            }
            $stmt->execute();
        }
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    private function delElements($elements) // $ids = quotes to del (array)
    {
        $stmt = dbConnexion::getInstance()->prepare('DELETE quotes WHERE id = :id;');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':id', $datas, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    private function addElements($elements) // array = quotes to add (array key[] = array key = fields, value = values)
    {
        $stmt = dbConnexion::getInstance()->prepare('INSERT INTO quotes (quote, author, source) VALUES (:quote, :author, :source);');
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
        $stmt = dbConnexion::getInstance()->prepare('UPDATE quotes SET quote = :quote, author = :author, source = :source WHERE id = :id');
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