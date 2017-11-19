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

	/**
     * Admin article edit controller.
     *
     * @param Application $app Silex application
     * @param Request $request the http request
     * @param $id id of article
     */
	public function editArticleAction(Application $app, Request $request, $id){	
	    $article = $app['dao.article']->find($id);
	    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
	    $articleForm->handleRequest($request);
	    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
	        $app['dao.article']->update($article);
	        $app['session']->getFlashBag()->add('success', 'The article was successfully updated.');
	    }
	    return $app['twig']->render('article_form.html.twig', array(
        		'title' => 'Edit article',
       			'articleForm' => $articleForm->createView()
       	));
	}

	/**
     * Admin article delete controller.
     *
     * @param Application $app Silex application
     * @param $id id of article
     */
	public function deleteArticleAction(Application $app, $id){	
		$app['dao.article']->delete($id);
	    $app['session']->getFlashBag()->add('success', 'The article was successfully removed.');
	    // Redirect to admin home page
	    return $app->redirect($app['url_generator']->generate('adminHome'));
	}

	/**
     * Admin article add controller.
     *
     * @param Application $app Silex application
     * @param Request $request the http request
     */
	public function addArticleAction(Application $app, Request $request){	
	    $article = new Article();
	    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
	    $articleForm->handleRequest($request);
	    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
	    	//now it is the conected user who is the author
	    	$user = $app['user'];
	    	$article->setAuthor($user->getId());
	        $article->setDate_publi(date('Y-m-d H:i:s'));
	        $app['dao.article']->insert($article);
	        $app['session']->getFlashBag()->add('success', 'The article was successfully created.');
	        // Redirect to admin home page
	    	return $app->redirect($app['url_generator']->generate('adminHome'));
	    }
	    return $app['twig']->render('article_form.html.twig', array(
        		'title' => 'Add article',
       			'articleForm' => $articleForm->createView()
       	));
	}

}
