<?php
namespace Controllers;

use Core\Controller;
use Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $productModel = new Product();
        $latestProducts = $productModel->getLatest(3);

        $this->view('home', [
            'title' => 'Home - PreshyMarketplace',
            'latestProducts' => $latestProducts
        ]);
    }

    public function about()
    {
        $this->view('about', [
            'title' => 'About Us - PreshyMarketplace'
        ]);
    }
}
?>