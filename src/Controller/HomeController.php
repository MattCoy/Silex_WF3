<?php
namespace WF3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use WF3\Domain\Article;
use WF3\Form\Type\ArticleType;
use WF3\Domain\User;
use WF3\Form\Type\UserRegisterType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class HomeController {
	/**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
	public function homePageAction(Application $app){	
	    $articles = $app['dao.article']->findALLOrderByDate('DESC');
	    $users = $app['dao.user']->findALL();
	    return $app['twig']->render('index.html.twig', array(
	    												'articles' => $articles,
	    												'users' => $users
	    ));
	}

	/**
     * Articles list page controller.
     *
     * @param Application $app Silex application
     */
	public function listAction(Application $app){
		$articles = $app['dao.article']->findALLWithUser();
	    return $app['twig']->render('list.html.twig', array(
	    												'articles' => $articles
	    ));
	}

	/**
     * Author details page controller.
     *
     * @param Application $app Silex application
     * @param $id the user id
     */
	public function authorAction(Application $app, Request $request, $id){		
	    $user = $app['dao.user']->find($id);
	    $articleFormView = null;
	    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
	        // A user is fully authenticated : he can add article
	        $article = new Article();
	        //for the moment the author is not the connected user but the user of the current page
	        //$user = $app['user'];
	        $article->setAuthor($user->getId());
	        $articleForm = $app['form.factory']->create(ArticleType::class, $article);
	        $articleForm->handleRequest($request);
	        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
	        	$article->setDate_publi(date('Y-m-d H:i:s'));
	            $app['dao.article']->insert($article);
	            $app['session']->getFlashBag()->add('success', 'Your article was successfully added.');
	        }
	        $articleFormView = $articleForm->createView();
	    }
	    $articles = $app['dao.article']->findByUser($id);
	    return $app['twig']->render('author.html.twig', array(
	    												'user' => $user,
	                                                    'articles' => $articles,
	                                                    'articleForm' => $articleFormView

	    ));
	}

	/**
     * Article details page controller.
     *
     * @param Application $app Silex application
     * @param $id the article id
     */
	public function articleAction(Application $app, $id){		
	    $article = $app['dao.article']->find($id);
	    $author = $app['dao.user']->find($article->getAuthor());
	    return $app['twig']->render('article.html.twig', array(
	                                                    'article' => $article,
	                                                    'author' => $author
	    ));
	}

	/**
     * login page controller.
     *
     * @param Application $app Silex application
     * @param $request Symfony\Component\HttpFoundation\Request
     */
	public function loginAction(Application $app, Request $request){
	    return $app['twig']->render('login.html.twig', array(
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username'),
	    ));
	}

	/**
     * User sign in controller.
     *
     * @param Application $app Silex application
     * @param Request $request the http request
     */
	public function signInAction(Application $app, Request $request){	
	    $user = new User();
	    $userForm = $app['form.factory']->create(UserRegisterType::class, $user);
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
	        //new users role is ROLE_USER by default
	        $user->setRole('ROLE_USER');
	        $app['dao.user']->insert($user);

	        //this code automatically login new user 
	        $token = new UsernamePasswordToken(
                $user, 
                $user->getPassword(), 
                'main',                 //key of the firewall you are trying to authenticate 
                array('ROLE_USER')
            );
            $app['security.token_storage']->setToken($token);

            // _security_main is, again, the key of the firewall
            $app['session']->set('_security_main', serialize($token));
            $app['session']->save(); // this will be done automatically but it does not hurt to do it explicitly
	        
	        
	        $app['session']->getFlashBag()->add('success', 'Hello ' .  $user->getUsername());
	        // Redirect to admin home page
	    	return $app->redirect($app['url_generator']->generate('homepage'));
	    }
	    return $app['twig']->render('user_register.html.twig', array(
        		'title' => 'Sign in',
       			'userForm' => $userForm->createView()
       	));
	}
}
