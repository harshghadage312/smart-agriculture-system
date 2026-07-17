<?php
include 'db.php';
requireLogin();
$id  = (int)$_GET['id'];
$uid = $_SESSION['user_id'];
// Only owner or admin can delete
mysqli_query($conn, "DELETE FROM products WHERE id=$id AND user_id=$uid");
header("Location: sell_product.php");
exit;
?>
