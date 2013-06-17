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
    private $id;
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
     * Return quote id
     * @access public
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * Return tags of quote
     * @access public
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Return date of quote
     * @access public
     * @return array
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Return date of quote
     * @access public
     * @return array
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * Add id for quote
     * @access public
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * Add tag(s) for quote
     * @access public
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Add date for quote
     * @access public
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Add date for quote
     * @access public
     * @return void
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }
}

/**
 * quoteQueries class
 * Used to populate quote object
 * @package quoteme
 * @author      Daniel Douat <daniel.douat@aelys-info.fr>
 * @link        http://www.aelys-info.fr
 */
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
        $nbQuotes       = $this->countElements();
        self::$nbQuotes = $nbQuotes[0]->nb;
    }

    /**
     * Create quote object
     * @param  array  $option quote sql options
     * @return array   $quote  one line by quote
     */
    public function getQuote($options = '')
    {
        $quotesList = $this->selElements($options);
        if (is_array($quotesList)) {
            $nbElements = count($quotesList);
            for ($i = 0; $i < $nbElements; $i++) {
                $quote[$i] = new quote();
                $quote[$i]->setId($quotesList[$i]->id);
                $quote[$i]->setText($quotesList[$i]->quote);
                $quote[$i]->setAuthor($quotesList[$i]->author);
                $quote[$i]->setSource($quotesList[$i]->source);
                $quote[$i]->setTags($quotesList[$i]->tags);
                $quote[$i]->setDate($quotesList[$i]->date);
                $quote[$i]->setPermalink($quotesList[$i]->permalink);
            }
            return $quote;
        }
    }

    /**
     * prepare code to add
     * @param  string $text   quote text, can't be empty
     * @param  string $author author or empty
     * @param  string $source quote source or empty (ex, book, internet)
     * @return array  $result an array contains all quotes added
     */
    public function addQuote($text, $author = '', $source = '', $tags = '')
    {
        if (!empty($text)) {
            $permalink = $this->smallHash(date(DATE_RFC822));
            $result[] = array('quote' => $text, 'author' => $author, 'source' => $source, 'tags' => $tags, 'permalink' => $permalink);
            //self::stack('insert', $result);
            $this->addElements($result);
        }
        return $result;
    }

    /**
     * Prepare quote to delete
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
     * Prepare quote to edit
     * @param  int    $id     sql id of quote
     * @param  string $text   quote (can't be empty)
     * @param  string $author author or empty
     * @param  string $source source or empty
     * @return array          an array contains all quotes edited
     */
    public function editQuote($id, $text, $author = '', $source = '', $tags = '')
    {
        if (is_int($id)) {
            if (!empty($text)) {
                $result[$id] = array('quote' => $text, 'author' => $author, 'source' => $source, 'tags' => $tags);
            }
        }
        return $result;
    }

    /**
     * Execute sql queries (insert, update and delete) stacked in self::$stack
     * @return void
     */
    public static function execStack()
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
     
    /**
     * Return sql queries stacked in self:$stack
     * @return array array of sql delete, update and insert queries
     */
    private static function getStacking()
    {
        return self::$stack;
    }

    /**
     * Add query in self::$stack
     * @param  string $type     insert, update or delete
     * @param  array  $elements array('sqlField' => 'fieldContent');
     * @return void
     */
    private static function stack($type, $elements)
    {
        if (!empty($type)) {
            self::$stack[] = array($type => $elements);
        }
    }

    /**
     * Execute SELECT sql query
     * @param  string $opt empty or array of sql option array('where' => 'id', 'whereOpt' => 'equal,10');
     * @return array an array of object result
     */
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
            $where = $this->constructWhere($opt['where'], $opt['whereOpt']);
            if (!empty($opt['and']) && !empty($opt['andOpt'])) {
                $where .= $this->constructWhere($opt['and'], $opt['andOpt'], TRUE);
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
            if ($opt['sort'] === 'random') $rand = ' JOIN ( SELECT FLOOR( COUNT( * ) * RAND( ) ) AS ValeurAleatoire FROM ' . self::$table . ' ) AS V ON ' . self::$table . '.id >= V.ValeurAleatoire';
            if (strpos($opt['sort'], ',')) {
                $sOpt = explode(',', $opt['sort']);
                $sort = ' ORDER BY ' . $sOpt[0] . ' ' .$sOpt[1];
            }
        }
        $query = 'SELECT id, quote, author, source, tags, permalink, date FROM ' . self::$table . $rand . $where . $sort . $limit . ';';
        $stmt  = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    /**
     * Return nb of all quotes
     * @return array array[0]->nb
     */
    private function countElements()
    {
        $stmt = dbConnexion::getInstance()->prepare('SELECT COUNT(*) AS nb FROM ' . self::$table .';');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        $stmt = NULL;
        return $result;
    }

    /**
     * Execute DELETE sql query
     * @param  array array('sqlField' => 'fieldContent');
     * @return void
     */
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

    /**
     * Execute INSERT sql query
     * @param array array('sqlField' => 'fieldContent');
     * @return void
     */
    private function addElements($elements) // array = quotes to add (array key[] = array key = fields, value = values)
    {
        $stmt = dbConnexion::getInstance()->prepare('INSERT INTO ' . self::$table . ' (quote, author, source, tags, permalink, date) VALUES (:quote, :author, :source, :tags, :permalink, NOW());');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':quote', $datas['quote'], PDO::PARAM_STR);
                $stmt->bindValue(':author', $datas['author'], PDO::PARAM_STR);
                $stmt->bindValue(':source', $datas['source'], PDO::PARAM_STR);
                $stmt->bindValue(':tags', $datas['tags'], PDO::PARAM_STR);
                $stmt->bindValue(':permalink', $datas['permalink'], PDO::PARAM_STR);
                //$stmt->bindValue(':date', $datas['date'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    /**
     * Execute UPDATE sql query
     * @param  array array('sqlField' => 'fieldContent');
     * @return void
     */
    private function editElements($elements)
    {
        $stmt = dbConnexion::getInstance()->prepare('UPDATE ' . self::$table .' SET quote = :quote, author = :author, source = :source, tags = :tags WHERE id = :id');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':id', $datas['id'], PDO::PARAM_INT);
                $stmt->bindValue(':quote', $datas['quote'], PDO::PARAM_STR);
                $stmt->bindValue(':author', $datas['author'], PDO::PARAM_STR);
                $stmt->bindValue(':source', $datas['source'], PDO::PARAM_STR);
                $stmt->bindValue(':tags', $datas['tags'], PDO::PARAM_STR);
                $stmt->bindValue(':permalink', $datas['permalink'], PDO::PARAM_STR);
                $stmt->bindValue(':date', $datas['date'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    /**
     * Return WHERE of AND sql structure
     * @param  string  $where    sql field
     * @param  string  $whereOpt where condition (ex like,lorem)
     * @param  boolean $and      if AND condition, set TRUE
     * @return string            sql structure
     */
    private function constructWhere($where, $whereOpt, $and = FALSE)
    {
        $cond   = ($and) ? 'AND' : 'WHERE';
        $opt    = explode(',', $whereOpt);
        $opt[0] = str_replace('minus', '<', $opt[0]);
        $opt[0] = str_replace('plus', '>', $opt[0]);
        $opt[0] = str_replace('equal', '=', $opt[0]);
        if ($opt[0] === 'like') {
            $opt[0] = strtoupper($opt[0]);
            $opt[1] = '%' . $opt[1] . '%';
        }
        return ' ' . $cond . ' ' .$where . ' ' .$opt[0] . ' "' . $opt[1] . '"';
    }
    /**
     * SmallHash via shaarli (sebsauvage)
     * @param  string $string [description]
     * @return string $hash   [description]
     */
    private function smallHash($string)
    {
        $hash = rtrim(base64_encode(hash('crc32', $string, TRUE)), '=');
        $hash = str_replace('+', '-', $hash); // Get rid of characters which need encoding in URLs.
        $hash = str_replace('/', '_', $hash);
        $hash = str_replace('=', '@', $hash);
        return $hash;
    }
}