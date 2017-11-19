<?php
namespace WF3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use WF3\Domain\Article;
use WF3\Form\Type\ArticleType;
use WF3\Domain\User;
use WF3\Form\Type\UserType;

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
        		'title' => 'Edit article',
       			'articleForm' => $articleForm->createView()
       	));
	}






	/**
     * Admin article edit controller.
     *
     * @param Application $app Silex application
     * @param Request $request the http request
     * @param $id id of user
     */
	public function editUserAction(Application $app, Request $request, $id){	
	    $user = $app['dao.user']->find($id);
	    $userForm = $app['form.factory']->create(UserType::class, $user);
	    $userForm->handleRequest($request);
	    if ($userForm->isSubmitted() && $userForm->isValid()) {
	    	//$user est l'objet créé à partir des données envoyées via le formulaire
	    	//on récupère le password en clair
	    	$plainPassword = $user->getPassword();
	        // on va chercher l'objet encoder
	        $encoder = $app['security.encoder_factory']->getEncoder($user);
	        // on encode le mdp en clair
	        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
	        //on remplace le mdp en clair par celui encrypté
	        $user->setPassword($password);
	        //on maj
	        $app['dao.user']->update($user);
	        $app['session']->getFlashBag()->add('success', 'The user was successfully updated.');
	    }
	    return $app['twig']->render('user_form.html.twig', array(
        		'title' => 'Edit user',
       			'userForm' => $userForm->createView()
       	));
	}

	/**
     * Admin article delete controller.
     *
     * @param Application $app Silex application
     * @param $id id of user
     */
	public function deleteUserAction(Application $app, $id){	
		$app['dao.user']->delete($id);
	    $app['session']->getFlashBag()->add('success', 'The user was successfully removed.');
	    // Redirect to admin home page
	    return $app->redirect($app['url_generator']->generate('adminHome'));
	}

	/**
     * Admin user add controller.
     *
     * @param Application $app Silex application
     * @param Request $request the http request
     */
	public function addUserAction(Application $app, Request $request){	
	    $user = new User();
	    $userForm = $app['form.factory']->create(UserType::class, $user);
	    $userForm->handleRequest($request);
	    if ($userForm->isSubmitted() && $userForm->isValid()) {
	    	// generate a random salt value
	        $salt = substr(md5(time()), 0, 23);
	        $user->setSalt($salt);
	        //get plain password 
	        $plainPassword = $user->getPassword();
	        // find the default encoder
	        $encoder = $app['security.encoder.bcrypt'];
	        // compute the encoded password
	        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
	        $user->setPassword($password);
	        $app['dao.user']->insert($user);
	        $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
	        // Redirect to admin home page
	    	return $app->redirect($app['url_generator']->generate('adminHome'));
	    }
	    return $app['twig']->render('user_form.html.twig', array(
        		'title' => 'Add user',
       			'userForm' => $userForm->createView()
       	));
	}
}
