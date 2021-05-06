<?php

use App\NumberHelper;
use App\TableHelper;
use App\URLHelper;

define('PER_PAGE', 20);

require 'vendor/autoload.php';

$pdo  = new PDO("sqlite:./data.sql", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$query = "SELECT * FROM products";
//pour éviter les injections on décompose notre query avec des paramètres au lieu de met $_GET['q']
//puis on passe par une requête préparé
$params = [];

$queryCount = "SELECT COUNT(id) as count FROM products";
//les champs que l'on peux trier
$sortable = ["id", "name", "city", "price", "address"];

//recherche par ville
if(!empty($_GET['q'])){
    $query .= " WHERE city LIKE :city";
    $queryCount .= " WHERE city LIKE :city";
    $params['city'] = '%' . $_GET['q'] . '%';
}

//tri par ville
if(!empty($_GET['sort']) && in_array($_GET['sort'], $sortable)){
    $direction = $_GET['dir'] ?? 'asc';
    if(!in_array($direction, ['asc', 'desc'])){
        $direction = 'asc';
    }
    $query .= " ORDER BY " .$_GET['sort']. " $direction";
}

//pagination
$page= (int)($_GET['p'] ?? 1);
$offset = ($page-1) * PER_PAGE;

$query .= " LIMIT ".PER_PAGE. " OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products=$stmt->fetchAll(); 


$stmt = $pdo->prepare($queryCount);
$stmt->execute($params);
$count=(int)$stmt->fetch()['count'];
$pages = ceil($count/PER_PAGE);

http_build_query(array_merge($_GET, ['p'=>3]));

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <title>Biens immobiliers</title>
</head>
<body class="p-4">

    <h1>Les biens immobiliers</h1>
    <form action="" class="mb-4">
        <div class="form-group">
            <input type="text" class="form-control" name="q" placeholder="Rechercher par ville" value="<?= htmlentities($_GET['q'] ?? null) ?>">
        </div>
        <button class="btn btn-primary">Rechercher</button>
    </form>
    <table class="table tabe-striped">
        <thead>
            <tr>
                <th><?= TableHelper::sort('id', 'ID', $_GET)?></th>
                <th><?= TableHelper::sort('name', 'Nom', $_GET)?></th>
                <th><?= TableHelper::sort('price', 'Prix', $_GET)?></th>
                <th><?= TableHelper::sort('city', 'Ville', $_GET)?></th>
                <th><?= TableHelper::sort('address', 'Adresse', $_GET)?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product):?>
            <tr>
                <td><?=$product['id'] ?></td>
                <td><?=$product['name'] ?></td>
                <td><?= NumberHelper::price($product['price']) ?></td>
                <td><?=$product['city'] ?></td>
                <td><?=$product['address'] ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php if($pages > 1 && $page > 1) : ?>
        <a href="?<?= URLHelper::withParam($_GET, "p", $page - 1) ?>" class="btn btn-primary">Page suivante</a>
    <?php endif ?>
    <?php if($pages > 1 && $page < $pages) : ?>
        <a href="?<?= URLHelper::withParam($_GET, "p", $page + 1) ?>" class="btn btn-primary">Page suivante</a>
    <?php endif ?>
    
</body>
</html>