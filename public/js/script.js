/* add click listeners to buttons */
document.getElementById('import_tab_button').addEventListener('click', showImportTab, false);
document.getElementById('search_tab_button').addEventListener('click', showSearchTab, false);
document.getElementById('article_button').addEventListener('click', getArticleByApi, false);
document.getElementById('search_button').addEventListener('click', searchByApi, false);

/* fill the links table at start */
getLinksTableByApi();

// functions ---------------------------------------------------------------------------------

function showImportTab()
{
    var searchTab = document.getElementById('search_tab');
    searchTab.style.display = 'none';

    var importTab = document.getElementById('import_tab');
    importTab.style.display = 'block';

    var importButton = document.getElementById('import_tab_button');
    importButton.classList.add("active");

    var searchButton = document.getElementById('search_tab_button');
    searchButton.classList.remove("active");
}

function showSearchTab()
{
    var importTab = document.getElementById('import_tab');
    importTab.style.display = 'none';

    var searchTab = document.getElementById('search_tab');
    searchTab.style.display = 'block';

    var searchButton = document.getElementById('search_tab_button');
    searchButton.classList.add("active");

    var importButton = document.getElementById('import_tab_button');
    importButton.classList.remove("active");
}

function getArticleByApi()
{
    var name = document.getElementById('article_name').value;
    if(name == '') {
        alert('Пожалуйста, укажите название статьи');
        return;
    }
    if(/^[\d.,:]*$/.test(name)) {
        alert('Статья с таким названием не найдена');
        return;
    }

    var request = new XMLHttpRequest(),
        url = '/api/import_article/' + name,
        response;
    request.responseType =	"json";
    request.open("POST", url, true);
    request.addEventListener("readystatechange", () => {
        if(request.readyState === 4 && request.status === 200) { 
            response = request.response;
            if(!response.code) {
                alert('Статья с таким названием не найдена');
                return;
            }
            if(response.code == 'already exist') {
                alert('Эта статья уже импортирована');
                return;
            } else {
                document.getElementById('import_info').innerHTML = response.result;
                getLinksTableByApi();
            }
        }
    });
    request.send(name);
}

function getLinksTableByApi()
{
    var request = new XMLHttpRequest(),
        url = '/api/get_links_table',
        response,
        linksTable = document.getElementById('links_table');

    request.responseType =	"json";
    request.open("POST", url, true);
    request.addEventListener("readystatechange", () =>
    {
        if(request.readyState === 4 && request.status === 200) { 
            response = request.response;
            linksTable.innerHTML = response.tableHTML;
        }
    });
    request.send();
}

function searchByApi()
{
    var keyword = document.getElementById('search_keyword').value;
    if(keyword == '') {
        alert('Пожалуйста, укажите ключевое слово для поиска');
        return;
    }

    document.getElementById('article_text').innerHTML = '';

    var request = new XMLHttpRequest(),
        url = '/api/search_by_keyword/' + keyword,
        response,
        row,
        rowId,
        searchResult = document.getElementById('search_result'),
        resultsTable = document.getElementById('result_table');

    request.responseType =	"json";
    request.open("POST", url, true);
    request.addEventListener("readystatechange", () => {
        if(request.readyState === 4 && request.status === 200) {
            response = request.response;
            searchResult.innerHTML = response.searchResult;
            if(response.code) {
                resultsTable.innerHTML = response.resultTable;
                setEventListeners(response.articlesNumber);
            } else {
                resultsTable.innerHTML = '';
            }
        }
    });
request.send(keyword);
}

function setEventListeners(rowsNumber)
{
    var dataName;
    
    for (let i = 0; i < rowsNumber; i++) {
        idNumber = String(i);
        rowId = 'result_row_' + idNumber;
        row = document.getElementById(rowId);
        row.addEventListener('click', getArticleTextByApi);
    }
}

function getArticleTextByApi(event)
{
    var name = event.target.getAttribute('data-name');
    var request = new XMLHttpRequest(),
        url = '/api/article_text/' + name,
        articleText = document.getElementById('article_text');

    request.responseType =	"json";
    request.open("POST", url, true);
    request.addEventListener("readystatechange", () => {
        if(request.readyState === 4 && request.status === 200) {
            articleText.innerText = request.response.text;
        }
    });
    request.send(name);
}