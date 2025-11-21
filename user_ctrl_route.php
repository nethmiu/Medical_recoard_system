<?php 
if(isset($_SESSION['user_id'])){
    $id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if($role === 'admin'){
        header("location: dashboard_admin.php");
    }
    elseif($role === 'patient'){
        header("location: dashboard_patient.php");
    }
    elseif($role === 'doctor'){
        header("location: dashboard_doctor.php");
    }

}

?>