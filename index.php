<?php

// https://yookassa.ru/developers/payment-acceptance/getting-started/quick-start?codeLang=php
// https://git.yoomoney.ru/projects/SDK/repos/yookassa-sdk-php/browse

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /** @var array $products */
    /** @var $pdo */
    $id = $_POST['id'];
    $price = $products[$id]['price'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $pdo->prepare("INSERT INTO orders (name, email, price) VALUES (?,?,?)");
    $stmt->execute([$name, $email, $price]);
    $order_id = $pdo->lastInsertId();

    try {
        $client = new \YooKassa\Client();
        $client->setAuth(SHOP_ID, API_KEY);
        $response = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $price,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => SUCCESS_URL,
                ),
                'capture' => true,
                'description' => "Заказ №{$order_id}",
                'metadata' => [
                    'orderNumber' => $order_id,
                ],
            ),
            uniqid('', true)
        );
        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();
        header("Location: {$confirmationUrl}");
        die;
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/errors.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    }

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yookassa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <?php foreach ($products as $id => $product): ?>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $product['title']; ?></h5>
                        <p class="card-text"><?= $product['price']; ?></p>
                        <a href="?id=<?= $id; ?>" class="btn btn-primary buy-product" data-bs-toggle="modal"
                           data-bs-target="#buy" data-id="<?= $id; ?>">Buy</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="buy" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="id" id="productId">
                        <button type="submit" class="btn btn-primary">Buy</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let buttons = document.querySelectorAll('.buy-product');
    buttons.forEach(item => {
        item.addEventListener('click', (e) => {
            document.getElementById('productId').value = item.dataset.id;
        });
    });
</script>
</body>
</html>
