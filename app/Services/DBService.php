<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Link;
use App\Models\Word;

/**
 * Service class for DB
 */
class DBService
{
    /**
     * save article data to db including tables for article and for words
     * 
     * @param array $article
     * ODT-like array for article
     * 
     * @return void
     */
    public static function saveDbAction($article)
    {
        /* check if this article is already imported */
        if(Article::where('name', $article['name'])->exists()) {
            return false;
        }

        /* save an article */
        $newArticleData = [
            'name' => $article['name'],
            'link' => $article['link'],
            'text' => $article['text'],
            'memory' => $article['memory'],
            'words_number' => $article['wordsNumber']
        ];
        $storedArticle = Article::create($newArticleData);

        /* save an array of words */
        foreach($article['words'] as $word => $number)
        {
            $dbWord = Word::firstOrCreate(['word' => $word]);
            $wordId = $dbWord->id;

            Link::create([
                'article_id' => $storedArticle->id,
                'word_id' => $wordId,
                'entry_number' => $number
            ]);
        }

        return true;
    }

    /**
     * get list of articles ordered by keyword
     * 
     * @param string $keyword
     * 
     * @return array 
     * 'list' => Illuminate\Database\Eloquent\Collection $articlesList of articles
     * 'entries' => array $entries of entry numbers for each article
     * 'entry_number' => int $entryNumber
     */
    public static function getArticlesByWord($keyword)
    {
        $dbWord = Word::where('word', $keyword)->first();
        $wordId = $dbWord->id;
        $links = Link::where('word_id', $wordId)
            ->orderByDesc('entry_number')
            ->get();

        $articlesList = [];
        $entries = [];
        $entryNumberTotal = 0;
        foreach($links as $link) {
            $articlesList[] = $link->article;
            $entries[] = $link->entry_number;
            $entryNumberTotal += $link->entry_number;
        }
        $data = ['list' => $articlesList, 'entries' => $entries, 'entry_number' => $entryNumberTotal];
        return $data;
    }
}