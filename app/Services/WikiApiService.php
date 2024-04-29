<?php

namespace App\Services;

/**
 * Service class for wiki API tasks
 */
class WikiApiService
{
    /**
     * get an article by WIKI API service 
     * 
     * @param string $name
     * article name
     * 
     * @return array $article
     * An array contents article info
     */
    public static function getArticle($name)
    {
        if((strlen($name) < 3) OR (!is_string($name))) {
            return['code' => false];
        }

        $article = array();

        $request = 'https://ru.wikipedia.org/w/rest.php/v1/page/' . $name;
        
        $ch = curl_init($request);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $responce = curl_exec($ch);
        curl_close($ch);
        
        $phpResponce = json_decode($responce, true);

        if(isset($phpResponce['httpCode'])) {
            return['code' => false];
        }
        $text = htmlspecialchars($phpResponce['source']);

        $parsedText = self::articleParser($text);

        $article['code'] = true;
        $article['name'] = $name;
        $article['link'] = 'https://ru.wikipedia.org/wiki/' . $name;
        $article['text'] = $parsedText['text'];
        $article['memory'] = self::memPresentation($parsedText['memory']);
        $article['wordsNumber'] = $parsedText['wordsNumber'];
        $article['words'] = self::countWords($parsedText);

        return $article;
    }

    /**
     * get the array of words from wiki text
     * 
     * @param string $text in wiki format
     * 
     * @return array $parsedText
     */
    private static function articleParser($text)
    {
        /**
         * result array
         * @var array
         * 
         * $parsedText['text'] => plain text
         * $parsedText['wordsNumber'] => total count of words
         * $parsedText['memory'] => int amount of memory for this text in bytes
         * $parsedText['words'] => array of arrays, where:
         *      key = first word symbol,
         *      value = indexed array of words
         *      like:   $parsedText['words']['Л'][0] = 'Липецк',
         *              $parsedText['words']['Л'][1] = 'Лермонтов'
         *              e.t.c.
         */
        $parsedText = [];

        /**
         * next word in text
         * @var string
         */
        $word = '';

        /**
         * array of text chars to remove
         * @var array
         */
        $rejectedChars = ['{', '}', '[', ']', '|', '*', '+', '='];

        $textAsArray = mb_str_split($text);
        $textLength = count($textAsArray);
        $parsedText['wordsNumber'] = 0;
        $parsedText['memory'] = 0;
        $parsedText['text'] = '';
        $parsedText['words'] = [];

        for($position=0; $position < $textLength; $position++)
        {
            $char = $textAsArray[$position];

            if(preg_match('/[а-яА-ЯёЁ]/', $char)) {
                $word .= $char;
                $parsedText['memory']++; // rus letters takes 2 bytes
            } elseif(preg_match('/[a-zA-Z0-9]/', $char)) {
                $word .= $char;
            } elseif($word !== '') {
                $word = mb_strtolower($word);
                $parsedText['words'][mb_substr($word, 0, 1)][] = $word;
                $parsedText['wordsNumber']++;
                $word = '';
            }
            
            if(in_array($char, $rejectedChars, true)) {
                $parsedText['text'] .= ' ';
            } else {
                $parsedText['text'] .= $char;
            }            
            $parsedText['memory']++; // add 1 byte to memory
        }
        return $parsedText;
    }

    /**
     * make an array of unique words and count their number in the text
     * 
     * @param array $parsedText
     * DTO-like array of article data
     * 
     * @return array $uniqueWords of unique words & their number:
     * [string word => int number]
     */
    private static function countWords($parsedText)
    {
        $uniqueWords = [];
        foreach($parsedText['words'] as $nextFirstSymbol)
        {
            $tempWordsArray = [];

            /* count number of words for this first symbol */
            foreach($nextFirstSymbol as $word)
            {
                if(isset($tempWordsArray[$word])) {      
                    $tempWordsArray[$word]++;
                } else {
                    $tempWordsArray[$word] = 1;
                }
            }

            /* save results */
            $uniqueWords = array_merge($uniqueWords, $tempWordsArray);
        }
        return $uniqueWords;
    }

    /**
     * make correct data presentation for memory amount
     * 
     * @param int $memory
     * 
     * @return string
     */
    private static function memPresentation($memory)
    {
        if($memory < 1024) {
            return (strval($memory) . ' b');
        } elseif ($memory < 1048576) {
            return(strval(round($memory / 1024, 2)) . ' Kb');
        } else {
            return(strval(round($memory / 1048576, 2)) . ' Mb');
        }
    }
}