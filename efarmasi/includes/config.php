<?php
class Database {
    private $host = "localhost";
    private $db_name = "efarmasi";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Forward Chaining Engine yang Diperbaiki
class ForwardChaining {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function processConsultation($symptoms, $patientData) {
        try {
            // Get active rules from database
            $query = "SELECT * FROM forward_chaining_rules WHERE is_active = 1 ORDER BY confidence_level DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $appliedRules = [];
            $recommendations = [];
            
            // Apply forward chaining dengan prioritas confidence tertinggi
            foreach ($rules as $rule) {
                $conditions = json_decode($rule['conditions'], true);
                
                if ($this->checkConditions($conditions, $symptoms, $patientData)) {
                    $appliedRules[] = $rule['id'];
                    $conclusions = json_decode($rule['conclusions'], true);
                    
                    // Get medicine details from database
                    $medicineDetails = $this->getMedicineDetails($conclusions['medicine'] ?? '');
                    
                    $recommendations[] = [
                        'rule_id' => $rule['id'],
                        'rule_name' => $rule['rule_name'],
                        'confidence' => $rule['confidence_level'],
                        'medicine' => $conclusions['medicine'] ?? '',
                        'dosage' => $conclusions['dosage'] ?? '',
                        'frequency' => $conclusions['frequency'] ?? '',
                        'duration' => $conclusions['duration'] ?? '',
                        'max_daily' => $conclusions['max_daily'] ?? '',
                        'warning' => $conclusions['warning'] ?? '',
                        'indications' => $medicineDetails['indications'] ?? '',
                        'contraindications' => $medicineDetails['contraindications'] ?? '',
                        'side_effects' => $medicineDetails['side_effects'] ?? ''
                    ];
                    
                    // Stop after finding high confidence match (confidence > 0.8)
                    if ($rule['confidence_level'] > 0.8) {
                        break;
                    }
                }
            }
            
            // Jika tidak ada rekomendasi, berikan saran umum
            if (empty($recommendations)) {
                $recommendations[] = [
                    'rule_id' => 0,
                    'rule_name' => 'Konsultasi Dokter',
                    'confidence' => 0.9,
                    'medicine' => '',
                    'dosage' => '',
                    'warning' => 'Berdasarkan gejala yang Anda alami, disarankan untuk berkonsultasi langsung dengan dokter atau apoteker untuk penanganan yang lebih tepat.',
                    'indications' => 'Konsultasi medis profesional',
                    'contraindications' => '',
                    'side_effects' => ''
                ];
            }
            
            return [
                'applied_rules' => $appliedRules,
                'recommendations' => $recommendations
            ];
            
        } catch(PDOException $exception) {
            throw new Exception("Error processing consultation: " . $exception->getMessage());
        }
    }
    
    private function getMedicineDetails($medicineName) {
        if (empty($medicineName)) return [];
        
        try {
            $query = "SELECT indications, contraindications, side_effects FROM medicines WHERE name LIKE ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute(["%$medicineName%"]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch(PDOException $e) {
            return [];
        }
    }
    
    private function checkConditions($conditions, $symptoms, $patientData) {
        // Check symptom conditions
        if (isset($conditions['symptoms'])) {
            $requiredSymptoms = is_array($conditions['symptoms']) ? $conditions['symptoms'] : [$conditions['symptoms']];
            foreach ($requiredSymptoms as $symptom) {
                if (!in_array($symptom, $symptoms)) {
                    return false;
                }
            }
        }
        
        // Check other conditions
        foreach ($conditions as $key => $value) {
            if ($key !== 'symptoms') {
                if (!$this->checkCondition($key, $value, $patientData)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    private function checkCondition($condition, $expected, $patientData) {
        if (!isset($patientData[$condition])) {
            return false;
        }
        
        $actual = $patientData[$condition];
        
        // Handle comparison operators
        if (is_string($expected)) {
            if (strpos($expected, '>') !== false) {
                $threshold = floatval(str_replace('>', '', $expected));
                return floatval($actual) > $threshold;
            } elseif (strpos($expected, '<') !== false) {
                $threshold = floatval(str_replace('<', '', $expected));
                return floatval($actual) < $threshold;
            } elseif (strpos($expected, '>=') !== false) {
                $threshold = floatval(str_replace('>=', '', $expected));
                return floatval($actual) >= $threshold;
            } elseif (strpos($expected, '<=') !== false) {
                $threshold = floatval(str_replace('<=', '', $expected));
                return floatval($actual) <= $threshold;
            }
        }
        
        return $actual == $expected;
    }
}
?>