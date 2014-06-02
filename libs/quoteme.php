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
    private $tags;
    private $date;
    private $permalink;

    function __call($method, $elements) {
        $var = strtolower(substr($method, 3));
        if (!strncasecmp($method, 'get', 3)) {
            return $this->$var;
        }
        if (!strncasecmp($method, 'set', 3)) {
            $this->$var = $elements[0];
        }
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
        self::$table    = $GLOBALS['config']['dbTable'];
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
    public function delQuote($permalink) // si la quote est supprimée, on retourne celle-ci au cas ou on veuille revenir en arrière
    {
        if (!empty($permalink)) {
            $result[] = $permalink;
            $this->delElements($result);
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
    public function editQuote($permalink, $text, $author = '', $source = '', $tags = '')
    {
        if (!empty($permalink)) {
            if (!empty($text)) {
                $result[$permalink] = array('quote' => $text, 'author' => $author, 'source' => $source, 'tags' => $tags);
                $this->editElements($result);
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
        if (!is_array($opt)) $opt = array('sort' => 'random', 'limit' => 1);
        if (!empty($opt['where']) && !empty($opt['whereOpt'])) {
            $where = $this->constructWhere($opt['where'], $opt['whereOpt']);
            if (!empty($opt['and']) && !empty($opt['andOpt'])) {
                $where .= $this->constructWhere($opt['and'], $opt['andOpt'], TRUE);
            }
        }
        if (!empty($opt['limit'])) {
            if (strpos($opt['limit'], ',') !== FALSE) {
                list($limitMin, $limitMax) = explode(',', $limit);
            }
            else {
                $limitMin = $opt['limit'];
            }
            $limit = ' LIMIT ' . rtrim($limitMin . ',' .$limitMax, ',');
        }
        if (!empty($opt['sort'])) {
            if ($opt['sort'] === 'random') $rand = ' JOIN ( SELECT FLOOR( COUNT( * ) * RAND( ) ) AS ValeurAleatoire FROM ' . self::$table . ' ) AS V ON ' . self::$table . '.id >= V.ValeurAleatoire';
            if (strpos($opt['sort'], ',')) {
                list($field, $order) = explode(',', $opt['sort']);
                $sort = ' ORDER BY ' . $field . ' ' .$order;
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
        $stmt = dbConnexion::getInstance()->prepare('DELETE FROM ' . self::$table .' WHERE permalink = :permalink;');
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->bindValue(':permalink', $datas, PDO::PARAM_INT);
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
        $stmt = dbConnexion::getInstance()->prepare('UPDATE ' . self::$table .' SET quote = :quote, author = :author, source = :source, tags = :tags WHERE permalink = :permalink');
        if (is_array($elements)) {
            foreach ($elements as $permalink => $datas) {
                $stmt->bindValue(':quote', $datas['quote'], PDO::PARAM_STR);
                $stmt->bindValue(':author', $datas['author'], PDO::PARAM_STR);
                $stmt->bindValue(':source', $datas['source'], PDO::PARAM_STR);
                $stmt->bindValue(':tags', $datas['tags'], PDO::PARAM_STR);
                $stmt->bindValue(':permalink', $permalink, PDO::PARAM_STR);
                //$stmt->bindValue(':date', $datas['date'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    /**
     * Return WHERE of AND sql structure
     * @param  string  $field    sql field
     * @param  string  $string   where condition (ex like,lorem)
     * @param  boolean $and      if AND condition, set TRUE
     * @return string            sql structure
     */
    private function constructWhere($field, $string, $and = FALSE)
    {
        $cond = ($and) ? 'AND' : 'WHERE';
        list($test, $string) = explode(',', $string);
        $test = str_replace('minus', '<', $test);
        $test = str_replace('plus', '>', $test);
        $test = str_replace('equal', '=', $test);
        if ($test === 'like') {
            $test = strtoupper($test);
            $string = '%' . $string . '%';
        }
        return ' ' . $cond . ' ' .$field . ' ' .$test . ' "' . $string . '"';
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