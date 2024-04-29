<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Word;
use App\Services\DBService;
use App\Services\ViewService;
use Illuminate\Http\Request;
use App\Services\WikiApiService;

class ApiController extends Controller
{
    /**
     * Prepare import tab links table HTML code
     *
     * @return $linksTableHTML
     * HTML code of table
     * in json format
     */
    public function getLinksTable()
    {
        $articles = Article::all();        

        $linksTableHTML = ViewService::linksTableRender($articles);

        return json_encode(['tableHTML' => $linksTableHTML]);
    }

    /**
     * downloads new article
     * 
     * @param string $name
     * name of new article
     * 
     * @return $article
     * ODT-like array for article
     * in json format
     */
    public function getNewArticle($name)
    {
        $start = microtime( true );

        $article = WikiApiService::getArticle($name);

        // return json_encode(['code' => true, 'result' => $article]);

        if(!DBService::saveDbAction($article)) {
            $article['code'] = 'already exist';
        }

        $time = sprintf( '%.6f sec.', microtime( true ) - $start );

        $article['result'] =
            'Импорт завершен<br/>
            <br/>
            Найдена статья по адресу: ' . $article['link'] . '<br/>
            Время обработки: ' . $time . '<br/>
            Размер статьи: ' . $article['memory'] . '<br/>
            Кол-во слов: ' . $article['wordsNumber'];
        
        return json_encode($article);
    }

    /**
     * get results of search handle
     * 
     * @param string $keyword
     * 
     * @return array $articlesListHTML
     * list of articles containing keyword rendered to HTML
     * in json format
     */
    public function searchByKeyword($keyword)
    {        
        $data = DBService::getArticlesByWord($keyword);
        
        $articlesList = ViewService::articlesListRender($data);

        return json_encode($articlesList);
    }

    public function getArticleText($name)
    {
        $article = Article::where('name', $name)->first();
        return json_encode(['text' => $article->text]);
    }
}