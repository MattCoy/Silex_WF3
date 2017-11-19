<?php
namespace WF3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use WF3\Domain\Article;
use WF3\Form\Type\ArticleType;

class AdminController {
	/**
     * Admin Home page controller.
     *
     * @param Application $app Silex application
     */
	public function indexAction(Application $app){	
	    $articles = $app['dao.article']->findAll();
	    $users = $app['dao.user']->findAll();
	    return $app['twig']->render('admin.html.twig', array(
	        'articles' => $articles,
	        'users' => $users));
	}

	
}
