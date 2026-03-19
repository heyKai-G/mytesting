<?php
    $full_name    = trim($_POST['fullName']    ?? '');
    $age          = trim($_POST['age']         ?? '');
    $address      = trim($_POST['address']     ?? '');
    $date         = trim($_POST['date']        ?? '');
    $service_type = $_POST['serviceType']      ?? [];
    $copies       = $_POST['copies']           ?? [];
    $urgency      = $_POST['urgency']          ?? [];
 
    $total          = 0;
    $errors         = array();
    $services_count = 0;
    $copies_count   = 0;
    $urgent_count   = 0;
    $normal_count   = 0;
    $reports        = array();
    $service_prices = array(
        "Barangay Clearance"    => 100,
        "Indigency Certificate" => 50,
        "Residency Certificate" => 80,
        "Business Permit"       => 500
    );
    $valid_service_types = array_keys($service_prices);
    $valid_urgencies     = ['normal', 'urgent'];
 
    // -------------------------------------------------------
    // VALIDATIONS
    // -------------------------------------------------------
 
    // Full Name: required, letters and spaces only
    if ($full_name === '') {
        $errors[] = "Full name is required.";
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $full_name)) {
        $errors[] = "Full name must contain letters only.";
    }
 
    // Age: required, numeric, realistic (18-100)
    if ($age === '') {
        $errors[] = "Age is required.";
    } elseif (!ctype_digit($age)) {
        $errors[] = "Age must be a whole number.";
    } elseif ((int)$age < 18 || (int)$age > 100) {
        $errors[] = "User must be between 18 and 100 years old to continue.";
    }
 
    // Address: required
    if ($address === '') {
        $errors[] = "Address is required.";
    }
 
    // Date: required, valid calendar date, must not be in the future
    if ($date === '') {
        $errors[] = "Date of Request is required.";
    } else {
        $input_date = DateTime::createFromFormat('Y-m-d', $date);
        $today      = new DateTime('today');
        if (!$input_date || $input_date->format('Y-m-d') !== $date) {
            $errors[] = "Date of Request is not a valid date.";
        } elseif ($input_date > $today) {
            $errors[] = "Date of Request must not be in the future.";
        }
    }
 
    // Service rows: all 3 required
    for ($i = 0; $i < 3; $i++) {
        $count   = $i + 1;
        $service = trim($service_type[$i] ?? '');
        $copy    = trim($copies[$i]       ?? '');
        $urg     = trim($urgency[$i]      ?? '');
 
        if ($service === '') {
            $errors[] = "Service Type in row {$count} is required.";
        } elseif (!in_array($service, $valid_service_types, true)) {
            $errors[] = "Service Type in row {$count} is invalid.";
        }
 
        if ($copy === '') {
            $errors[] = "Copies in row {$count} is required.";
        } elseif (!ctype_digit($copy) || (int)$copy < 1) {
            $errors[] = "Copies in row {$count} must be at least 1.";
        }
 
        if ($urg === '') {
            $errors[] = "Urgency in row {$count} is required.";
        } elseif (!in_array($urg, $valid_urgencies, true)) {
            $errors[] = "Urgency in row {$count} is invalid.";
        }
    }
 
    // -------------------------------------------------------
    // SHOW ERRORS & STOP if any
    // -------------------------------------------------------
    if (!empty($errors)) {
        echo '<div class="alert alert-danger" role="alert">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
        exit; // Stop here — don't run calculations with bad data
    }
 
    // -------------------------------------------------------
    // CALCULATIONS (only reached if no errors)
    // -------------------------------------------------------
    for ($i = 0; $i < 3; $i++) {
        if ($urgency[$i] == "urgent") {
            $urgency_fee = 50;
            $urgent_count++;
        } else {
            $urgency_fee = 0;
            $normal_count++;
        }
 
        $base_fee = isset($service_type[$i]) ? $service_prices[$service_type[$i]] : 0;
 
        if ($base_fee > 0) {
            $services_count++;
        }
 
        $subtotal      = ($base_fee + $urgency_fee) * (int)$copies[$i];
        $copies_count += (int)$copies[$i];
        $total        += $subtotal;
 
        $reports[$i]             = array();
        $reports[$i]["services"] = $service_type[$i];
        $reports[$i]["copies"]   = (int)$copies[$i];
        $reports[$i]["urgency"]  = $urgency[$i];
        $reports[$i]["subtotal"] = $subtotal;
    }
 
    // Transaction category
    if ($total > 1500) {
        $category = "High Value Transaction";
    } elseif ($total > 1000) {
        $category = "Large Transaction";
    } elseif ($total > 300 && $total < 1000) {
        $category = "Medium Transaction";
    } else {
        $category = "Small Transaction";
    }
 
    // -------------------------------------------------------
    // OUTPUT
    // -------------------------------------------------------
    echo "<div>";
    echo "<p>Total Services Requested: $services_count</p>";
    echo "<p>Total Copies Requested: $copies_count</p>";
    echo "<p>Total Priority Requests: $urgent_count</p>";
    echo "<p>Total Normal Requests: $normal_count</p>";
    echo "<p>Grand Total: &#8369;$total</p>";
    echo "<p>Transaction Category: $category</p>";
    echo "</div>";
 
    echo "<h2>Report</h2>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Service Type</th>";
    echo "<th>Copies</th>";
    echo "<th>Urgency</th>";
    echo "<th>Subtotal</th>";
    echo "</tr>";
 
    foreach ($reports as $report) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($report['services']) . "</td>";
        echo "<td>" . $report['copies']                     . "</td>";
        echo "<td>" . htmlspecialchars($report['urgency'])  . "</td>";
        echo "<td>&#8369;" . $report['subtotal']            . "</td>";
        echo "</tr>";
    }
 
    echo "<tr>";
    echo "<td colspan='3'><strong>Total</strong></td>";
    echo "<td><strong>&#8369;$total</strong></td>";
    echo "</tr>";
    echo "</table>";
?>