<?php

namespace WF3\DAO;

use Doctrine\DBAL\Connection;
use WF3\Domain\Article;

class ArticleDAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     */
    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from articles order by id desc";
        $result = $this->db->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['id'];
            $articles[$articleId] = $this->buildArticle($row);
        }
        return $articles;
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \WF3\Domain\Article
     */
    private function buildArticle(array $row) {
        $article = new Article();
        $article->setId($row['id']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);
        $article->setDate_publi($row['date_publi']);
        $article->setAuthor($row['author']);
        return $article;
    }
}