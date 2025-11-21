<?php
include 'includes/header.php';

if ($_SESSION['role'] !== 'doctor') {
    header("location: login.php");
    exit;
}

if (!isset($_GET['record_id'])) {
    header("location: dashboard_doctor.php");
    exit;
}

$record_id = $_GET['record_id'];
$patient_id = $_GET['patient_id'];

// Check if delete confirmed via GET parameter 'confirm=yes'
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $conn->prepare("DELETE FROM medical_records WHERE record_id = ?");
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Record deleted successfully.</p>";
        echo "<script>window.location.href='view_records.php?patient_id=".$patient_id."';</script>";

    } else {
        echo "<p style='color:red;'>Error deleting record.</p>";
    }

    $stmt->close();
    include 'includes/footer.php';
    exit;
}
?>

<script>
    if (confirm("Are you sure you want to delete this medical record? This action cannot be undone.")) {
        // Redirect to same page but with confirm=yes to proceed deletion
        window.location.href = "<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?record_id=' . $record_id . '&confirm=yes' . '&patient_id=' . $patient_id ?>";
    } else {
        // Redirect back to dashboard if cancel
        window.location.href = "view_medical_rec.php?patient_id=<?php echo $patient_id ?>&rec_id=<?php echo $record_id ?>";
    }
</script>

<?php
include 'includes/footer.php';
?>
