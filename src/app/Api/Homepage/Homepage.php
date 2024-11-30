<?php

namespace App\Api\Homepage;

use Phalapi\Api;



class Homepage extends Api {

    public function index() {
        // 改为页面展示
        header("Content-type: text/html; charset=utf-8");
        include(API_ROOT . '/src/view/homepage/index.php');
        exit(0);
    }
}

