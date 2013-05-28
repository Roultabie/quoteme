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
    private $quote;
    private $authors;
    private $sources;

    function __construct()
    {
        //$this->setQuote('');
        //$this->setAuthor('');
        //$this->setSource('');
    }

    /**
     * Return quote
     * @access public
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
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
    public function setQuote($quote)
    {
        $this->quote = $quote;
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
            $result = $this->loadElement('all');
        }

        /* get specific quote */
        elseif (is_int($option)) {
            $result = $this->loadElement('one', $option);
        }

        /* get multiple quotes */
        elseif ($option[0] == "(") {
            $quantity = trim($option, '()');
            $result = $this->loadElement('limit', $quantity);
        }

        /* get multi specific quotes */
        elseif (strpos(';', $option) !== FALSE) {
            //convert (10;48; 92;) to 10, 48, 92
            // $ids = str_replace(' ', '', $option);
            // $ids = str_replace(';', ',', $ids);
            // $ids = trim($ids, ',');
            // $ids = trim($ids);
            // More compact
            $ids = trim(trim(str_replace(';', ',', str_replace(' ', '', $option)), ','));
            $result = $this->loadElement('multi', $ids);
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
                
                /*$quotes = $this->loadElement('all', 'id');
                if (is_array($quotes)) {
                    $id = array_rand($quotes, 1);
                    $result = $this->getQuote($id);
                }*/

            }
        }

        if (is_array($result)) {
            $quote = new quote();
        }
        else $quote = '';

        return $quote;
    }

    public function addQuote($quote, $author = '', $source = '')
    {

        return $result;
    }

    public function delQuote($id) // si la quote est supprimée, on retourne celle-ci au cas ou on veuille revenir en arrière
    {
        return $result;
    }

    public function editQuote($id, $elements)
    {

    }

    // End # public functions -------------------------------------------------
        
    // Start # private functions ----------------------------------------------

    private function loadElement($options, $fields = "")
    {
        // sql select all quote
    }

    private function delElement($array) // array = quotes to del (array of ids)
    {

    }

    private function addElement($array) // array = quotes to del (array type key = fields, value = values)
    {

    }

    private function editElement($array) // array multidimentional = quotes to del (array keys = ids of elements, array inside: key fields, values values)
    {

    }
}