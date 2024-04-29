<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>wiki-app</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Css styles -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    
    <body>
        <div class="wiki_tabs_wrapper">
            <div class="tab_selector">
                <button id="import_tab_button" class="wiki_tab_button active">
                    Импорт статей
                </button>
            </div>
            <div class="tab_selector">
                <button id="search_tab_button" class="wiki_tab_button">
                    Поиск
                </button>
            </div>
        </div>

        <div class="diff_line"></div>

        <div id="import_tab">
            <div class="wiki_search_wrapper">
                <div class="input_field">
                    <input type="text" id="article_name" placeholder=" Название" />
                </div>
                <div class="input_field">
                    <button id="article_button" class="input_button">Скачать статью</button>
                </div>
            </div>

            <div class="output_wrapper">
                <div id="import_info" class="output_field">
                    <!-- article import info output, content received from API -->
                </div>
            </div>

            <div class="diff_line"></div>

            <div id="links_table" class="links_table_wrapper">
                <!-- rendered links table output, content received from API -->
            </div>
        </div>

        <div id="search_tab">
            <div class="wiki_search_wrapper">
                <div class="input_field">
                    <input type="text" id="search_keyword" placeholder=" Ключевое слово" />
                </div>
                <div class="input_field">
                    <button id="search_button" class="input_button">Найти</button>
                </div>
            </div>

            <div class="diff_line"></div>

            <div class="search_result_wrapper">
                <div class="both_sides">
                    <div class="search_output_field">
                        <div id="search_result">
                            <!-- search result info output, content received from API -->
                        </div>
                        <div id="result_table">
                            <!-- table of search results, content received from API -->
                        </div>
                    </div>
                </div>
                <div class="both_sides">
                    <div class="output_wrapper">
                        <div id="article_text" class="text_output_field">
                            <!-- article text output, content received from API -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/script.js') }}"></script>
    </body>
</html>