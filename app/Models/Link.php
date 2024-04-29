<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['article_id' , 'word_id', 'entry_number'];

    /**
     * get linked article
     * @return Article
     */
    public function article() {
        return $this->belongsTo(Article::class);
    }
}
