
<?php

// Home page
$app->get('/', function () use ($app) {
    //$articles = $app['dao.article']->findALL();
    $articles = $app['dao.article']->findALLOrderByDate('DESC');
    return $app['twig']->render('index.html.twig', array('articles' => $articles));
});
