<?php include('../../examples/includes/header.php'); ?>


<?php
include 'Calendar.php';
$calendar = new Calendar('2024-05-12');
$calendar->add_event('Birthday', '2024-05-03', 1, 'green');
$calendar->add_event('Doctors', '2024-05-04', 1, 'red');
$calendar->add_event('Holiday', '2024-05-16', 7);
?>

<?=$calendar?>

<?php include('../../examples/includes/navbar.php'); ?>

<?php include('../../examples/includes/footer.php'); ?>