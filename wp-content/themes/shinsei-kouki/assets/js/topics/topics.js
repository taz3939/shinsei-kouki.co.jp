/**
 * お知らせ詳細ページ - .newsContent 内の h2 を抜粋し INDEX を先頭に挿入
 * data-show-index="0" の場合は目次を出力しない
 */
(function() {
  var article = document.querySelector('.newsArticle');
  if (article && article.getAttribute('data-show-index') === '0') {
    return;
  }

  var newsContent = document.querySelector('.newsArticle .newsContent');
  if (!newsContent) {
    return;
  }

  var h2List = newsContent.querySelectorAll('h2');
  if (!h2List.length) {
    return;
  }

  var nav = document.createElement('nav');
  nav.className = 'newsIndex';
  nav.setAttribute('aria-label', '目次');

  var title = document.createElement('p');
  title.className = 'newsIndexTitle';
  title.textContent = 'INDEX';

  var list = document.createElement('ol');
  list.className = 'newsIndexList';

  for (var i = 0; i < h2List.length; i++) {
    var h2 = h2List[i];
    var id = h2.id;
    if (!id) {
      id = 'section-' + i;
      h2.id = id;
    }

    var li = document.createElement('li');
    var a = document.createElement('a');
    a.href = '#' + id;
    a.textContent = h2.textContent;
    li.appendChild(a);
    list.appendChild(li);
  }

  nav.appendChild(title);
  nav.appendChild(list);
  newsContent.parentNode.insertBefore(nav, newsContent);
})();
