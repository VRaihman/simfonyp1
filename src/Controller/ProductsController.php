<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Modules\GetConfig;
use App\Modules\Products;
use App\Modules\EventCalendar as Calendar;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends AbstractController
{
	public function __construct(GetConfig $config)
	{
		$this->config = new $config;
	}
	
	
    /**
     * @Route("/{type}", defaults={"type": "index.html|index.asp"},)
     */
    public function indexPage($type)
    {
        return $this->render('tpl/index.html.twig', []);
    }    
	
	/**
     * @Route("/product/add/{date}", name="product")
     */
    public function productAdd($date = 'none', Products $products)
    {
		$productList = $products->getList();
		$productDayList = $products->loadDay($date);
		$endHtml = '';
		$tplStr = '<tr id="prod-[[pid]]"><td>[[num]]</td><td>[[name]]</td><td>[[amount]]</td><td><button id="button" type="button" class="btn btn-xs btn-success" onclick="ajaxAddProduct(\'[[pid]]\', \'[[date]]\');" >Add to Cart</button></td></tr>';
		$num = 1;
		foreach($productList as $key => $product){
			if(empty($productDayList[$key])){
				$endHtml .= str_replace(
					['[[num]]', '[[name]]', '[[amount]]', '[[pid]]', '[[date]]' ],
					[$num, $product['name'], $product['amount'], $key, $date],
					$tplStr
				);
				$num++;
			}
		}
		
		if($endHtml == ''){
			$endHtml = '<tr id="0"><td></td><td></td><td></td><td></td></tr>';
			
		}
		
		return $this->render('tpl/addproduct.html.twig', [
            'date' => $date,
            'endHtml' => $endHtml,
        ]);
    }	
	
	/**
     * @Route("/cart/{date}", name="cart")
     */
    public function cartList($date = 'none', Products $products)
    {
		$productDayList = $products->loadDay($date);
		
		$tplStr = '<tr id="prod-[[pid]]">
		<td></td>
		<td>[[name]]</td>
		<td>[[amount]]</td>
		<td>[[date]]</td>
		<td>[[updatedate]]</td>
		<td><button id="button" type="button" class="btn btn-xs btn-success" onclick="ajaxDeleteProduct(\'[[pid]]\', \'[[date]]\');" >Delete</button></td></tr>';
		$endHtml = '';
		$num = 0;
		foreach($productDayList as $key => $product){
			$endHtml .= str_replace(
				['[[num]]', '[[name]]', '[[amount]]', '[[pid]]', '[[date]]', '[[date]]', '[[updatedate]]' ],
				[$num, $product['name'], $product['amount'], $key, $date, $product['date'], $product['updatedate'],],
				$tplStr
			);
			$num++;
		}
		
        return $this->render('tpl/cart.html.twig', [
            'date' => $date,
			'tabelHtml' => $endHtml,
        ]);
    }    
	

    /**
     * @Route("/ajax/{type}", name="ajax")
     */
    public function jsonPage($type='all', Calendar $calendar, Products $products)
    {
		switch ($type) {
			case 'addProduct':
				return $products->addProduct();
			break;
			case 'deleteProduct':
				return $products->deleteProduct2Day();
			break;

			case 'getEvents':
			default:
				return $calendar->getEvents($products);
		}  
    }
}
