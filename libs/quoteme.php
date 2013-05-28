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
    private $authors;
    private $sources;

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
        return $this->authors;
    }

    /**
     * Return source of quote
     * @access public
     * @return array
     */
    public function getSource()
    {
        return $this->sources;
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
        $this->authors[] = $author;
    }

    /**
     * Add source(s) for quote
     * @access public
     * @return void
     */
    public function setSource($source)
    {
        $this->sources[] = $source;
    }
}

class quoteQueries
{

    private $elements;

    function __construct()
    {

    }

    /**
     * [getQuote description]
     * @param  string  $option quote options like all, number, (number), nb1:nbX
     * @param  boolean $random to ramdomize quote list set to TRUE
     * @return array   $quote  one line by quote
     */
    public function getQuote($option = '', $random = FALSE) // options : all, number: for one, (number): for quantity, nb1;nb2;nbX: for multiple,   random, multi: all,random or 10,random, or (57), random or 1;5,18,39,radom
    {
        /* get all quotes */
        if ($option == "all") {
            $result = $this->selElement('all');
        }

        /* get specific quote */
        elseif (is_int($option)) {
            $result = $this->selElement('one', $option);
        }

        /* get multiple quotes */
        elseif ($option[0] == "(") {
            $quantity = trim($option, '()');
            $result = $this->selElement('limit', $quantity);
        }

        /* get multi specific quotes */
        elseif (strpos(';', $option) !== FALSE) {
            // convert (10;48; 92;) to 10, 48, 92
            // $ids = str_replace(' ', '', $option);
            // $ids = str_replace(';', ',', $ids);
            // $ids = trim($ids, ',');
            // $ids = trim($ids);
            // More compact
            $ids = trim(trim(str_replace(';', ',', str_replace(' ', '', $option)), ','));
            $result = $this->selElement('multi', $ids);
        }

        /* get one randomized quote */
        elseif (empty($option)) {
            $random = TRUE;
        }

        // randomize quotes / get randomized quote
        if ($random === TRUE) {
            if (is_array($result)) {
                $result = shuffle($result);
            }
            else {

                // test de performances random
                // version 1
                
                $quotes = $this->getQuote('all');
                if (is_array($quotes)) {
                    $result = $quotes[array_rand($quotes, 1)];
                }

                // -------------------------------------------
                // version 2
                
                /*$quotes = $this->selElement('all', 'id');
                if (is_array($quotes)) {
                    $id = array_rand($quotes, 1);
                    $result = $this->getQuote($id);
                }*/

            }
        }

        if (is_array($result)) {
            $quote = new quote();
        }
        else {
            $quote = 'Error getQuote(), $result is empty !';
        }

        return $quote;
    }

    public function addQuote($quote, $author = '', $source = '')
    {
        if (!empty($quote)) {
            $result[] = array('quote' => $text, 'author' => $author, 'source' => $source);
        }
        return $result;
    }

    public function delQuote($id) // si la quote est supprimée, on retourne celle-ci au cas ou on veuille revenir en arrière
    {
        if (is_int($id)) {
            $result[] = $id;
        }
        return $result;
    }

    public function editQuote($id, $elements)
    {
        if (is_int($id)) {
            if (is_array($elements)) {
                foreach ($elements as $field => $value) {
                    $result[]
                }
            }
        }
    }

    // End # public functions -------------------------------------------------
        
    // Start # private functions ----------------------------------------------

    private function selElements($options, $fields = "")
    {
        // sql select all quote
    }

    private function delElements($array) // array = quotes to del (array of ids)
    {
        $stmt = dbConnexion::getInstance()->prepare("DELETE quotes WHERE id = :id");
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->execute('id' => $datas);
            }
        }
    }

    private function addElements($elements) // array = quotes to add (array key[] = array key = fields, value = values)
    {
        $stmt = dbConnexion::getInstance()->prepare("INSERT INTO quotes (quote, author, source) VALUES (:quote, :author, :source)");
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->execute('quote' => $datas['quote'], 'author' => $datas['author'], 'source' => $datas['source']);
            }
        }
    }

    private function editElements($elements) // array multidimentional = quotes to edit (array keys = ids of elements, array inside: key fields, values values)
    {
        $stmt = dbConnexion::getInstance()->prepare("UPDATE quotes SET quote = :quote, author = :author, source = :source WHERE id = :id");
        if (is_array($elements)) {
            foreach ($elements as $datas) {
                $stmt->execute('id' => $datas['id'], 'quote' => $datas['quote'], 'author' => $datas['author'], 'source' => $datas['source']);
            }
        }
    }
}