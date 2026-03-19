<?php
		$full_name = $_POST['fullName'];
		$age = $_POST['age'];
		$address = $_POST['address'];
		$date = $_POST['date'];
		$service_type = $_POST['serviceType'];
		$copies = $_POST['copies'];
		$urgency = $_POST['urgency'];
		
		$total = 0;
		$errors = array();
		$services_count = 0;
		$copies_count = 0;
		$urgent_count = 0;
		$normal_count = 0;
		$reports = array();
		$service_prices = array("Barangay Clearance" => 100, "Indigency Certificate" => 50, "Residency Certificate"  => 80,
		"Business Permit" => 500);
		//echo $age;
		//print_r($service_type);
		//print_r($copies);
		//print_r($urgency);
		
		if(empty($full_name)){
			$errors [] = "Full name is required";
		}
		if(empty($age)){
			$errors [] = "Age is required";
		}
		if(empty($address)){
			$errors [] = "Address is required";
		}
		if(empty($date)){
			$errors [] = "Date of Request is required";
		}
		if(!is_numeric($age) || $age < 18){
			$errors [] = "User must be 18 to continue";
		}
		
		for($i = 0; $i <3; $i++){
			$count = $i + 1;
			if(!isset($urgency[$i]) || empty($urgency[$i]) ){
				
				$errors [] = "Urgency Field $count is required";
			}
			if(!isset($service_type[$i]) || empty($service_type)){
				$errors [] = "Service Type $count is required";
			}
			if(!isset($copies[$i]) || $copies[$i] < 1){
				$errors [] = "Copies Field $count is required";
			}
		}
		
		if(!empty($errors)){
					echo '<div class = "alert alert-danger" role = "alert">';
					
					foreach($errors as $error){
						echo "<p>$error</p>";
					}
					echo '</div>';
				}
		
		
		for($i =0; $i <3; $i++){
			if($urgency [$i] == "urgent"){
				$urgency_fee = 50;
				$urgent_count ++;
				
			}
			else{
				$urgency_fee = 0;
				$normal_count ++;
			}
			$base_fee = isset($service_type[$i])? $service_prices[$service_type[$i]]: 0;
			
			if($base_fee > 0){
				$services_count ++;
			}
			/*switch ($service_type[$i]){
				case "Barangay Clearance":
				$base_fee = 100;
				$services_count ++;
				break;
				
				case "Indigency Certificate":
				$base_fee = 50;
				$services_count ++;
				break;
				
				case "Residency Certificate":
				$base_fee = 80;
				$services_count ++;
				break;
				
				case "Business Permit":
				$base_fee = 500;
				$services_count ++;
				break;
				default: 
				$base_fee = 0;
			}*/
			
			
			/*if($service_type[$i] == "Barangay Clerance"){
				$base_fee = 100;
			}
			else if($service_type[$i] == "Indigency Certificate"){
				
			}
			else if($service_type[$i] == "Residency Certificate"){
				$base_fee = 80;
			}
			else{
				$base_fee = 500;
			}*/
			
			$subtotal = ($base_fee + $urgency_fee) * (int)$copies[$i];
			//echo $subtotal."</br>";
			$copies_count +=(int)$copies[$i];
			//echo $copies_count."</br>";
			$total += $subtotal; 
			$reports[$i] = array();
			$reports[$i]["services"] = $service_type[$i];
			$reports[$i]["copies"] = (int)$copies[$i];
			$reports[$i]["urgency"] = $urgency [$i];
			$reports[$i]["subtotal"] = $subtotal;
			
		}
		//print_r($reports);
		
		if($total > 1500){
				$category = "High Value Transaction";
			}
			else if($total > 1000){
				$category = "Large Transaction";
			}
			else if($total > 300 && $total < 1000){
				$category = "Medium Transaction";
			}
			else{
				$category = "Small Transaction";
			}
		
		echo"<div>";
		echo"<p>Total Services Requested: $services_count</p>";
		echo"<p>Total Copies Request: $copies_count</p>";
		echo"<p>Total Priority Request: $urgent_count</p>";
		echo"<p>Total Normal Request: $normal_count</p>";
		echo"<p>Total Grand Total: $total</p>";
		echo"<p>Transaction Category: $category</p>";
		echo"</div>";
		echo($total);
		
		echo"<h2>Report</h2>";

		echo"<table>";
		echo"<tr>";
		echo"<th>Service Type</th>";
		echo"<th>Copies</th>";
		echo"<th>Urgency</th>";
		echo"<th>Subtotal</th>";
		echo"  </tr>";
		foreach($reports as $report){
			echo"<tr>";
			echo"<td>".$report['services']."</td>";
			echo"<td>".$report['copies']."</td>";
			echo"<td>".$report['urgency']."</td>";
			echo"<td>".$report['subtotal']."</td>";
			echo"</tr>";
		}
		echo"<tr>";
			echo"<td>Total</td>";
			echo"<td>".$total."</td>";
			echo"</tr>";
		echo"</table>";
		

?>
