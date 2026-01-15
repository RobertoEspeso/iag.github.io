<?php
header('Content-Type: application/json; charset=utf-8');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
  exit;
}

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid JSON"]);
  exit;
}

// Helpers
function clean_text($v) {
  return htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8');
}
function clean_email($v) {
  $v = trim((string)$v);
  return filter_var($v, FILTER_VALIDATE_EMAIL) ? $v : '';
}
function line($label, $value) {
  $value = trim((string)$value);
  if ($value === '') return '';
  return $label . ": " . $value . "\n";
}

// Anti-spam básico (honeypot opcional)
// Si después agregás un input oculto llamado "website", esto bloquea bots automáticamente.
if (!empty($data['website'])) {
  http_response_code(200);
  echo json_encode(["status" => "OK"]);
  exit;
}

// A dónde llega
$to = "reservations@iagtravel.com";

// Detectar tipo de formulario
$formType = isset($data['formType']) ? clean_text($data['formType']) : '';

if ($formType === 'contact') {

  $name     = clean_text($data['name'] ?? '');
  $company  = clean_text($data['company'] ?? '');
  $email    = clean_email($data['email'] ?? '');
  $phone    = clean_text($data['phone'] ?? '');
  $service  = clean_text($data['service'] ?? '');
  $comments = clean_text($data['comments'] ?? '');

  // Validación mínima server-side
  if ($name === '' || $company === '' || $email === '' || $service === '' || $comments === '') {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
  }

  $subject = "New Contact Request – " . ($company !== '' ? $company : 'Website');

  $body  = "NEW CONTACT REQUEST\n";
  $body .= "-------------------\n";
  $body .= line("Name", $name);
  $body .= line("Company", $company);
  $body .= line("Email", $email);
  $body .= line("Phone", $phone);
  $body .= line("Service", $service);
  $body .= "\nCOMMENTS:\n" . $comments . "\n";

  // Recomendación entregabilidad:
  // From debe ser del dominio. Reply-To es el usuario.
  $headers  = "From: IAG Travel Website <no-reply@iagtravel.com>\r\n";
  $headers .= "Reply-To: $email\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

  if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["status" => "OK"]);
  } else {
    http_response_code(500);
    echo json_encode(["error" => "Could not send email"]);
  }
  exit;
}

if ($formType === 'travel_profile') {

  // Personal
  $personal = $data['personal'] ?? [];
  $pName = clean_text($personal['name'] ?? '');
  $pLast = clean_text($personal['lastName'] ?? '');
  $pEmail = clean_email($personal['email'] ?? '');
  $pPhone = clean_text($personal['phone'] ?? '');

  if ($pName === '' || $pLast === '' || $pEmail === '') {
    http_response_code(400);
    echo json_encode(["error" => "Missing required personal info"]);
    exit;
  }

  $subject = "New Travel Profile – " . $pName . " " . $pLast;

  $body  = "NEW TRAVEL PROFILE\n";
  $body .= "------------------\n\n";

  // Personal Information
  $body .= "PERSONAL INFORMATION\n";
  $body .= line("Name", $pName);
  $body .= line("Last Name", $pLast);
  $body .= line("Email", $pEmail);
  $body .= line("Phone", $pPhone);
  $body .= "\n";

  // Passport
  $passport = $data['passport'] ?? [];
  $body .= "PASSPORT\n";
  $body .= line("Country of Issue", clean_text($passport['countryIssue'] ?? ''));
  $body .= line("Date of Issue", clean_text($passport['dateIssue'] ?? ''));
  $body .= line("First Name", clean_text($passport['firstName'] ?? ''));
  $body .= line("Middle Name", clean_text($passport['middleName'] ?? ''));
  $body .= line("Last Name", clean_text($passport['lastName'] ?? ''));
  $body .= line("Date of Birth", clean_text($passport['dateBirth'] ?? ''));
  $body .= line("Nationality", clean_text($passport['nationality'] ?? ''));
  $body .= line("Passport Number", clean_text($passport['passportNumber'] ?? ''));
  $body .= line("Valid Until", clean_text($passport['validUntil'] ?? ''));
  $body .= "\n";

  // Drivers License
  $dl = $data['driversLicense'] ?? [];
  $body .= "DRIVERS LICENSE (USA ONLY)\n";
  $body .= line("First Name", clean_text($dl['firstName'] ?? ''));
  $body .= line("Middle Name", clean_text($dl['middleName'] ?? ''));
  $body .= line("Last Name", clean_text($dl['lastName'] ?? ''));
  $body .= line("Date of Birth", clean_text($dl['dateBirth'] ?? ''));
  $body .= line("State", clean_text($dl['state'] ?? ''));
  $body .= line("License Number", clean_text($dl['licenseNumber'] ?? ''));
  $body .= line("Date of Issue", clean_text($dl['dateIssue'] ?? ''));
  $body .= line("Valid Until", clean_text($dl['validUntil'] ?? ''));
  $body .= "\n";

  // Frequent Flyer
  $ff = $data['frequentFlyer'] ?? [];
  $body .= "FREQUENT FLYER PROGRAMS\n";
  $body .= line("Program Name/Status", clean_text($ff['programNameStatus'] ?? ''));
  $body .= line("Account Number", clean_text($ff['accountNumber'] ?? ''));
  $body .= "\n";

  // Airplane Preferences
  $ap = $data['airplanePreferences'] ?? [];
  $body .= "AIRPLANE PREFERENCES\n";
  $body .= line("Class", clean_text($ap['class'] ?? ''));
  $body .= line("Position", clean_text($ap['position'] ?? ''));
  $body .= "\n";

  // Hotel Programs
  $hotelPrograms = $data['hotelPrograms'] ?? [];
  $body .= "HOTEL PROGRAMS\n";
  if (is_array($hotelPrograms) && count($hotelPrograms) > 0) {
    $i = 1;
    foreach ($hotelPrograms as $hp) {
      if (!is_array($hp)) continue;
      $hn = clean_text($hp['hotelName'] ?? '');
      $mn = clean_text($hp['membership'] ?? '');
      if ($hn === '' && $mn === '') continue;
      $body .= "  - Hotel #" . $i . "\n";
      $body .= line("    Hotel Name", $hn);
      $body .= line("    Membership Number", $mn);
      $i++;
    }
  } else {
    $body .= "(none)\n";
  }
  $body .= "\n";

  // Hotel Preferences
  $hpref = $data['hotelPreferences'] ?? [];
  $body .= "HOTEL PREFERENCES\n";
  $body .= line("Stars", clean_text($hpref['stars'] ?? ''));
  $body .= line("Room Type", clean_text($hpref['roomType'] ?? ''));
  $body .= "\n";

  // Rental Car Programs
  $rental = $data['rentalCarPrograms'] ?? [];
  $body .= "RENTAL CAR PROGRAMS\n";
  if (is_array($rental) && count($rental) > 0) {
    $i = 1;
    foreach ($rental as $rc) {
      if (!is_array($rc)) continue;
      $company = clean_text($rc['company'] ?? '');
      $account = clean_text($rc['account'] ?? '');
      if ($company === '' && $account === '') continue;
      $body .= "  - Rental #" . $i . "\n";
      $body .= line("    Company", $company);
      $body .= line("    Account Number", $account);
      $i++;
    }
  } else {
    $body .= "(none)\n";
  }
  $body .= "\n";

  $headers  = "From: IAG Travel Website <no-reply@iagtravel.com>\r\n";
  $headers .= "Reply-To: $pEmail\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

  if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["status" => "OK"]);
  } else {
    http_response_code(500);
    echo json_encode(["error" => "Could not send email"]);
  }
  exit;
}

// Si llega acá, no sabemos qué form fue
http_response_code(400);
echo json_encode(["error" => "Unknown formType"]);
exit;
?>
