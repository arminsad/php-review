<?php 

require(__DIR__ . '/vendor/autoload.php');
// pgsql:host=(host);port=(port);dbname=(dbname);user=(user);password=(password)

if (file_exists(__DIR__ . '/.env')){
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$pdo = new PDO($_ENV['PDO_CONNECTION_STRING']);

$sql = "
SELECT invoices.id, invoice_date, total, first_name, last_name 
FROM invoices
INNER JOIN customers
ON invoices.customer_id = customers.id
";

$statement = $pdo->prepare($sql); // creating a prepared statement
$statement->execute();
$invoices = $statement->fetchAll(PDO::FETCH_OBJ);

//var_dump($invoices);


?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Customer</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($invoices as $invoice) : ?>
        <tr>
            <td>
                <?php echo $invoice->id ?>
            </td>
            <td>
                <?php echo $invoice->invoice_date ?>
            </td>
            <td>
                <?php echo $invoice->total ?>
            </td>
            <td>
                <?php echo "{$invoice->first_name} {$invoice->last_name}" ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>