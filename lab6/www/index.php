<?php
require 'vendor/autoload.php';

use App\ElasticExample;

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Лабораторная 6: Блог (Elasticsearch)</h1>";

$elastic = new ElasticExample();
$index = 'blog_posts';

$elastic->deleteIndex($index);

$posts = [
    ['id' => 1, 'title' => 'Введение в БФУ', 'content' => 'Новый неокампус', 'author' => 'Иван Петров'],
    ['id' => 2, 'title' => 'Ишанов для начинающих', 'content' => 'Дифференциальные уравнения, доброе утро, садитесь.', 'author' => 'Олег Щащ'],
    ['id' => 3, 'title' => 'Основы ВИ Семенова', 'content' => 'Полнотекстовый поиск и аналитика матанализа', 'author' => 'Антон Баранов'],
];

echo "<h2>Индексация записей блога:</h2>";
foreach ($posts as $post) {
    $id = $post['id'];
    unset($post['id']);
    try {
        $res = $elastic->indexDocument($index, $id, $post);
        echo "<p>✅ Запись #$id добавлена</p>";
    } catch (\Exception $e) {
        echo "<p style='color:red;'>❌ Ошибка при добавлении #$id: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<h2>Поиск по блогу:</h2>";
$q = $_GET['q'] ?? 'PHP';
echo "<form method='GET' style='margin-bottom:15px;'>
        <input name='q' value='" . htmlspecialchars($q) . "' placeholder='Поиск по названию, тексту или автору'>
        <button type='submit'>Найти</button>
      </form>";

try {
    $result = $elastic->search($index, $q);
    $data = json_decode($result, true);

    if (isset($data['hits']['hits'])) {
        echo "<ul>";
        foreach ($data['hits']['hits'] as $hit) {
            $doc = $hit['_source'];
            echo "<li><strong>" . htmlspecialchars($doc['title']) . "</strong> (Автор: " . htmlspecialchars($doc['author']) . ")<br>
                  <small>" . htmlspecialchars($doc['content']) . "</small></li>";
        }
        echo "</ul>";
        echo "<p>Найдено записей: " . $data['hits']['total']['value'] . "</p>";
    } else {
        echo "<p style='color:red;'>Ошибка: " . htmlspecialchars($result) . "</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red;'>Ошибка поиска: " . htmlspecialchars($e->getMessage()) . "</p>";
}