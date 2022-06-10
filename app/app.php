<?php
declare(strict_types=1);

use App\Api\CatApi;
use App\Api\RestApi;

require './bootstrap.php';

try {
    $return = [];

    $cat_api = new CatApi();
    $fact_response = $cat_api->getCatFact();
    $return['cat_fact'] = $fact_response['fact'];

    $rest_api = new RestApi();
    $users = $rest_api->get(1);
    $user = $users[0] ?? ['email' => 'test'];
    $return['get'] = $users;

    $post = $rest_api->post($user);
    $return['post'] = $post;

    $user['name'] = 'Samir';
    $put = $rest_api->put($user['id'], $user);
    $return['put'] = $put;

    $patch = $rest_api->patch($user['id'], $user);
    $return['patch'] = $patch;

    $delete = $rest_api->delete($user['id']);
    $return['delete'] = $delete;

    $get_by_id = $rest_api->getById($user['id']);
    $return['get_by_id'] = $get_by_id;

    // if someone likes print_r() output
    if (!empty($_GET['echo'])) {
        echo '<pre>';
        print_r($return);
        echo '</pre>';
    } else {
        dd($return);
    }
} catch (\Throwable $e) {
    dump($e->getMessage(), $e->getTrace());
}
