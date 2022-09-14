<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Header;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractDashboardController
{


    #[Route('/admin', name: 'admin')]

        public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
               $url = $routeBuilder->setController(OrderCrudController::class)->generateUrl();

                return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
       // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
      // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BestStore')

            // you can include HTML contents too (e.g. to link to an image)
            ->setTitle('<img src="..."> BestStore <span class="text-small"></span>')

            // the path defined in this method is passed to the Twig asset() function
            ->setFaviconPath('favicon.svg')


            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            //->renderSidebarMinimized()

            // by default, all backend URLs include a signature hash. If a user changes any
            // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
            // triggers an error. If this causes any issue in your backend, call this method
            // to disable this feature and remove all URL signature checks
            ->disableUrlSignatures()

            // by default, all backend URLs are generated as absolute URLs. If you
            // need to generate relative URLs instead, call this method
            ->generateRelativeUrls()
            ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class),
            MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Order::class),
            MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class),
            MenuItem::linkToCrud('Produits', 'fa fa-tag', Product::class),
            MenuItem::linkToCrud('Transporteurs', 'fa fa-truck', Carrier::class),
            MenuItem::linkToCrud('Header', 'fa fa-desktop', Header::class)


        ];
    }

}
