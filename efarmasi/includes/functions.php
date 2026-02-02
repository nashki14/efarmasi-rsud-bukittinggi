<?php
// Utility functions for PHARMEDICE

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[0-9+\-\s()]{10,}$/', $phone);
}

function format_date($date_string, $format = 'd F Y') {
    $date = new DateTime($date_string);
    return $date->format($format);
}

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function log_activity($db, $user_id, $activity, $details = '') {
    try {
        $query = "INSERT INTO activity_logs (user_id, activity, details, ip_address) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id, $activity, $details, get_client_ip()]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function send_notification($user_id, $title, $message, $type = 'info') {
    // In a real application, this would send email/push notifications
    // For now, we'll just log it
    error_log("Notification for user $user_id: $title - $message");
    return true;
}

function calculate_age($birth_date) {
    $birthday = new DateTime($birth_date);
    $today = new DateTime();
    $age = $today->diff($birthday);
    return $age->y;
}

function generate_prescription_id() {
    return 'RX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function check_drug_interaction($db, $drug1, $drug2) {
    // This would check against a drug interaction database
    // For now, return a mock response
    $interactions = [
        'major' => ['Warfarin', 'Aspirin'],
        'moderate' => ['Simvastatin', 'Clarithromycin'],
        'minor' => ['Paracetamol', 'Ibuprofen']
    ];
    
    foreach ($interactions as $level => $drugs) {
        if (in_array($drug1, $drugs) && in_array($drug2, $drugs)) {
            return [
                'has_interaction' => true,
                'level' => $level,
                'description' => "Interaksi $level antara $drug1 dan $drug2"
            ];
        }
    }
    
    return ['has_interaction' => false];
}
?>