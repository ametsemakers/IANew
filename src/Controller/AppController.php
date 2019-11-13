<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use BitBag\SyliusCmsPlugin\Entity\Block;
use BitBag\SyliusCmsPlugin\Entity\Page;
use App\Service\KeywordGenerator;
use Sylius\Bundle\CoreBundle\Form\Type\ContactType;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
//use Pagerfanta\Adapter\DoctrineORMAdapter;
//use Pagerfanta\Pagerfanta;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        $page = $this->getDoctrine()
            ->getRepository(PAGE::class)
            ->findLatestBlogArticle()
        ;

        return $this->render('main/home.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/blog/{page}", name="blog", requirements={"page"="\d+"})
     * @Route("/blog/{keyword}/{page}", name="blog_keyword", requirements={"page"="\d+"})
     */
    public function blog(Request $request, KeywordGenerator $keywordGenerator,int $page = 1, string $keyword = null)
    {
        if (!null == $request->query->get('page'))
        {
            $page = $request->query->get('page');
        }
        // if a keyword is passed, get the list of articles by keyword
        if (!null == $request->attributes->get('keyword'))
        {
            $keyword = $request->attributes->get('keyword');

            // repository takes 3 arguments: nb of results per page, active page, keyword to search
            $pages = $this->getDoctrine()
            ->getRepository(PAGE::class)
            ->findBlogArticlesByKeyword(5, $page, $keyword)
            ;

            $keywords = $keywordGenerator->getPageKeywords($pages);

            return $this->render('main/blog.html.twig', [
                'pages'    => $pages,
                'keywords' => $keywords,
            ]);
        }
        // else get all articles
        else
        {
            $articles = $this->getDoctrine()
            ->getRepository(BLOCK::class)
            ->findBlogBlocks()
            ;
            $pages = $this->getDoctrine()
                ->getRepository(PAGE::class)
                ->findBlogArticles(5, $page)
            ;

            $keywords = $keywordGenerator->getPageKeywords($pages);

            return $this->render('main/blog.html.twig', [
                'articles' => $articles,
                'page'     => $page,
                'pages'    => $pages,
                'keywords' => $keywords,
            ]);
        }   
    }

    /**
     * @Route("/blog/{id}/{slug}", name="blog_show", methods={"GET"})
     */
    public function blogShow(Request $request, $id)
    {
        if (!null == $request->query->get('id'))
        {
            $id = $request->query->get('id');
        }

        $page = $this->getDoctrine()
            ->getRepository(PAGE::class)
            ->findBlogArticleById($id)
        ;

        return $this->render('main/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/history/{page}", name="history", defaults={"page" = 1})
     */
    public function history()
    {
        // $queryBuilder = $this->getDoctrine()
        //      ->getRepository(PAGE::class)
        //      ->findBlogArticles();
        // $adapter = new DoctrineORMAdapter($queryBuilder, true, false);
        // $pager = new Pagerfanta($adapter);
        // $pager->setMaxPerPage(10);

        $pager = $this->getDoctrine()
             ->getRepository(PAGE::class)
             ->findBlogArticles();

        return $this->render('main/history.html.twig', [
            'pager'    => $pager,
        ]);
    }
}