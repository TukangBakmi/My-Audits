<!DOCTYPE html>
<html lang="en">

<?php
$titleDoc = "Maybank | Dashboard";
include_once '../static/head.php';
include_once '../static/navbar.php';
include_once '../backend/func/dashboard.php';
?>

<link rel="stylesheet" href="../static/css/index.css">

<body class="dashboard bg-user text-black">
    <?php include_once '../views/components/dashboard.php'; ?>
</body>

</html>

<script>
    $("#dashboard").addClass("nav-user-active");
</script>