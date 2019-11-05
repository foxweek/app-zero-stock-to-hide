<?php

namespace Apps\ZeroStockToHide;

use Sellfino\Shopify;

Class App
{

  public function install()
  {

    Shopify::hook('products/update', 'ZeroStockToHide/webhook/products/update', 'ZeroStockToHide');

  }

  public function uninstall()
  {

    Shopify::unhook('ZeroStockToHide/webhook/products/update', 'ZeroStockToHide');
    
  }

  public function router($route)
  {
    
    if ($route == 'webhook/products/update') {

      $this->run();

    }

  }

  public function run()
  {

    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['published_at']) {

      $zero = true;

      foreach ($data['variants'] as $variant) {

        if ($variant['inventory_quantity'] > 0) {

          $zero = false;

        }

      }

      if ($zero) {

        $put = [
          'product' => [
            'id' => $data['id'],
            'published_at' => null
          ]
        ];

        Shopify::request('products/' . $data['id'], $put, 'PUT');

      }

    }

  }
  
}