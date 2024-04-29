<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Word;

/**
 * Service class for views
 */
class ViewService
{
    /**
     * render a table for imort tab
     * 
     * @param array $articles
     * array of Article objects
     * 
     * @return string $tableHTML
     * ready ".linksTable" table for import tab view
     */
    public static function linksTableRender($articles)
    {
        $tableHTML = "
            <div class='links_table_header'>
                Название статьи
            </div>
            <div class='links_table_header'>
                Ссылка
            </div>
            <div class='links_table_header'>
                Размер статьи
            </div>
            <div class='links_table_header'>
                Кол-во слов
            </div>";

        $rowStripe = 0;

        foreach($articles as $article)
        {
            $tableHTML .= "
                <div class='links_table_cell_$rowStripe'>
                    $article->name
                </div>
                <div class='links_table_cell_$rowStripe'>
                    <a href='$article->link'>$article->link</a>
                </div>
                <div class='links_table_cell_$rowStripe'>
                    $article->memory
                </div>
                <div class='links_table_cell_$rowStripe'>
                    $article->words_number
                </div>";

            if($rowStripe) {
                $rowStripe = 0;
            } else {
                $rowStripe = 1;
            }
        }

        return $tableHTML;
    }

    /**
     * return HTML code for view the list of found articles
     * 
     * @param array $data
     * 'list' => Illuminate\Database\Eloquent\Collection $articlesList
     * 'entries' => array of int entry numbers for each article
     * 'entry_number' => $entryNumber total entry number
     * 
     * @return array $result
     * bool code => show if word is found
     * int entryNumber - total entry number
     * int articlesNumber
     * string searchResult => html total search result
     * string resultTable => html result table
     */
    public static function articlesListRender($data)
    {
        $result = [];
        $articlesList = $data['list'];
        $entries = $data['entries'];
        $entryNumber = $data['entry_number'];

        $result['code'] = ($entryNumber != 0);
        $result['entryNumber'] = $entryNumber;

        switch ($entryNumber % 10) {
            case '1':
                $wordEnding = 'е';
                break;

            case '2':
            case '3':
            case '4':
                $wordEnding = 'я';
                break;
            
            default:
                $wordEnding = 'й';
                break;
        }
        if((($entryNumber > 10)
            && ($entryNumber < 21))
            || (($entryNumber > 110)
            && ($entryNumber < 121))) {
                $wordEnding = 'й';
        }

        /* get total result html */
        $result['searchResult'] =
        '<div class="search_table_header">
            Найдено: ' . $entryNumber . ' совпадени'
            . $wordEnding . '
        </div>';

        /* prepare table html */
        $rowNumber = 0;
        $result['resultTable'] = '';

        $result['articlesNumber'] = 0;
        $rowStripe = 0;
        foreach($articlesList as $article)
        {
            // add tags. preparing html-id, class and data-name for js script
            $result['resultTable'] .= '
            <div id="result_row_' . $rowNumber
                . '" class="search_table_cell_' . $rowStripe
                . '" data-name="' . $article->name
                . '">';

                // add row content
                $result['resultTable'] .= $article->name
                . ' (' . $entries[$rowNumber]
                . ' вхождени';
                switch ($entries[$rowNumber] % 10) {
                    case '1':
                        $wordEnding = 'е)';
                        break;
            
                    case '2':
                    case '3':
                    case '4':
                        $wordEnding = 'я)';
                        break;
                        
                    default:
                        $wordEnding = 'й)';
                        break;
                }
                if(($entries[$rowNumber] > 10
                    && $entries[$rowNumber] < 21)
                    OR ($entries[$rowNumber] > 110
                    && $entries[$rowNumber] < 121)) {
                        $wordEnding = 'й)';
                }
                $result['resultTable'] .= $wordEnding . '
            </div>';

            if($rowStripe) {
                $rowStripe = 0;
            } else {
                $rowStripe = 1;
            }

            $result['articlesNumber']++;
            $rowNumber++;
        }
        return $result;
    }
}